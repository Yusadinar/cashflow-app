<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index()
    {
        $debts = auth()->user()->debts()->where('type', 'debt')->latest()->get();
        $receivables = auth()->user()->debts()->where('type', 'receivable')->latest()->get();
        $paymentMethods = auth()->user()->paymentMethods()->get();
        
        return view('debts.index', compact('debts', 'receivables', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:debt,receivable',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $debt = auth()->user()->debts()->create($validated);

        if ($request->filled('payment_method_id')) {
            $type = $validated['type'] === 'debt' ? 'income' : 'expense';
            $category = auth()->user()->categories()->firstOrCreate(
                ['name' => 'Debt/Receivable', 'type' => $type]
            );

            auth()->user()->transactions()->create([
                'category_id' => $category->id,
                'payment_method_id' => $request->payment_method_id,
                'type' => $type,
                'amount' => $validated['amount'],
                'description' => ($validated['type'] === 'debt' ? 'Borrowed from: ' : 'Lent to: ') . $validated['name'],
                'transaction_date' => now(),
            ]);
        }

        return back()->with('success', 'Record added successfully.');
    }

    public function update(Request $request, Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:debt,receivable',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'required|in:unpaid,paid',
        ]);

        $wasUnpaid = $debt->status === 'unpaid';
        $debt->update($validated);

        if ($wasUnpaid && $validated['status'] === 'paid' && $request->filled('payment_method_id')) {
            $type = $validated['type'] === 'debt' ? 'expense' : 'income'; // Paying debt = expense, Receiving receivable = income
            $category = auth()->user()->categories()->firstOrCreate(
                ['name' => 'Debt/Receivable Payment', 'type' => $type]
            );

            auth()->user()->transactions()->create([
                'category_id' => $category->id,
                'payment_method_id' => $request->payment_method_id,
                'type' => $type,
                'amount' => $validated['amount'],
                'description' => ($validated['type'] === 'debt' ? 'Paid debt to: ' : 'Received payment from: ') . $validated['name'],
                'transaction_date' => now(),
            ]);
        }

        return back()->with('success', 'Record updated successfully.');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $debt->delete();

        return back()->with('success', 'Record deleted successfully.');
    }
}
