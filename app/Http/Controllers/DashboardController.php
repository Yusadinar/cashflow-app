<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $transactions = $user->transactions()
            ->with(['category', 'paymentMethod'])
            ->whereBetween('transaction_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $paymentMethods = $user->paymentMethods;
        $initialBalance = $paymentMethods->sum('balance');

        $transactionSums = $user->transactions()
            ->selectRaw('payment_method_id, type, SUM(amount) as total')
            ->groupBy('payment_method_id', 'type')
            ->get();

        $transferSumsIn = $user->transfers()
            ->selectRaw('to_payment_method_id, SUM(amount) as total')
            ->groupBy('to_payment_method_id')
            ->get();
            
        $transferSumsOut = $user->transfers()
            ->selectRaw('from_payment_method_id, SUM(amount) as total')
            ->groupBy('from_payment_method_id')
            ->get();

        $income = $transactionSums->where('type', 'income')->sum('total') + $initialBalance;
        $expense = $transactionSums->where('type', 'expense')->sum('total');
        
        $balances = [];
        foreach ($paymentMethods as $pm) {
            $pmIncome = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'income')->sum('total');
            $pmExpense = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'expense')->sum('total');
            $transfersIn = $transferSumsIn->where('to_payment_method_id', $pm->id)->sum('total');
            $transfersOut = $transferSumsOut->where('from_payment_method_id', $pm->id)->sum('total');

            $balances[] = [
                'name' => $pm->name,
                'balance' => $pm->balance + $pmIncome - $pmExpense + $transfersIn - $transfersOut
            ];
        }

        $yearlyTransactions = $user->transactions()
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, type, SUM(amount) as total')
            ->groupByRaw('MONTH(transaction_date), type')
            ->get();

        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'income' => array_fill(0, 12, 0),
            'expense' => array_fill(0, 12, 0),
        ];

        foreach ($yearlyTransactions as $t) {
            $monthIndex = $t->month - 1;
            if ($t->type === 'income') {
                $chartData['income'][$monthIndex] = (float) $t->total;
            } else {
                $chartData['expense'][$monthIndex] = (float) $t->total;
            }
        }

        $yearlyInitialBalances = $user->paymentMethods()
            ->whereYear('updated_at', $year)
            ->selectRaw('MONTH(updated_at) as month, SUM(balance) as total')
            ->groupByRaw('MONTH(updated_at)')
            ->get();

        foreach ($yearlyInitialBalances as $b) {
            $monthIndex = $b->month - 1;
            $chartData['income'][$monthIndex] += (float) $b->total;
        }

        return view('dashboard', compact('transactions', 'income', 'expense', 'balances', 'month', 'year', 'chartData'));
    }
}
