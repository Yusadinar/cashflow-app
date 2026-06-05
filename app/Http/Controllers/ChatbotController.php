<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $message = $request->message;
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Gemini API Key belum diatur.']);
        }

        // Get user financial context
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
        
        $totalBalance = 0;
        $balancesList = [];
        foreach ($paymentMethods as $pm) {
            $pmIncome = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'income')->sum('total');
            $pmExpense = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'expense')->sum('total');
            $transfersIn = $transferSumsIn->where('to_payment_method_id', $pm->id)->sum('total');
            $transfersOut = $transferSumsOut->where('from_payment_method_id', $pm->id)->sum('total');

            $pmBalance = $pm->balance + $pmIncome - $pmExpense + $transfersIn - $transfersOut;
            $balancesList[] = "{$pm->name}: Rp " . number_format($pmBalance, 0, ',', '.');
            $totalBalance += $pmBalance;
        }

        // Also fetch debts and wishlists if available
        $debts = $user->debts ?? collect();
        $totalDebt = $debts->sum('amount');
        
        $wishlists = $user->wishlists ?? collect();
        $totalWishlist = $wishlists->sum('estimated_cost');

        // Fetch monthly breakdown for current year
        $year = date('Y');
        $yearlyTransactions = $user->transactions()
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, type, SUM(amount) as total')
            ->groupByRaw('MONTH(transaction_date), type')
            ->get();

        $monthlyData = [];
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        for($i=1; $i<=12; $i++) {
            $monthlyData[$i] = ['income' => 0, 'expense' => 0];
        }

        foreach ($yearlyTransactions as $t) {
            if ($t->type === 'income') {
                $monthlyData[$t->month]['income'] = (float) $t->total;
            } else {
                $monthlyData[$t->month]['expense'] = (float) $t->total;
            }
        }
        
        $monthlyList = [];
        foreach($monthlyData as $m => $data) {
            if($data['income'] > 0 || $data['expense'] > 0) {
                $monthlyList[] = $months[$m-1] . " $year: Pemasukan Rp " . number_format($data['income'], 0, ',', '.') . " | Pengeluaran Rp " . number_format($data['expense'], 0, ',', '.');
            } else {
                $monthlyList[] = $months[$m-1] . " $year: Rp 0";
            }
        }
        $monthlyText = implode("\n- ", $monthlyList);
        $currentDate = date('d F Y');

        // Fetch recent transactions for context
        $recentTransactions = $user->transactions()
            ->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->limit(30)
            ->get();
            
        $transactionList = [];
        foreach($recentTransactions as $tr) {
            $date = \Carbon\Carbon::parse($tr->transaction_date)->format('d-m-Y');
            $type = $tr->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
            $amount = "Rp " . number_format($tr->amount, 0, ',', '.');
            $desc = $tr->description ? " - {$tr->description}" : "";
            $cat = $tr->category ? " ({$tr->category->name})" : "";
            $transactionList[] = "[{$date}] {$type}: {$amount}{$cat}{$desc}";
        }
        $transactionText = empty($transactionList) ? "Belum ada transaksi." : implode("\n- ", $transactionList);

        $context = "Anda adalah asisten keuangan pribadi yang ramah dan cerdas. Anda membantu pengguna memahami kondisi keuangan mereka.\n" .
            "Hari ini adalah: {$currentDate}.\n" .
            "Data keuangan pengguna secara keseluruhan:\n" .
            "- Total Pemasukan (Semua Waktu): Rp " . number_format($income, 0, ',', '.') . "\n" .
            "- Total Pengeluaran (Semua Waktu): Rp " . number_format($expense, 0, ',', '.') . "\n" .
            "- Total Saldo Saat Ini: Rp " . number_format($totalBalance, 0, ',', '.') . "\n" .
            "- Rincian Saldo: " . implode(', ', $balancesList) . "\n" .
            "- Total Hutang: Rp " . number_format($totalDebt, 0, ',', '.') . "\n" .
            "- Total Kebutuhan Wishlist: Rp " . number_format($totalWishlist, 0, ',', '.') . "\n\n" .
            "Rincian Agregasi Transaksi Bulanan Tahun {$year}:\n" .
            "- " . $monthlyText . "\n\n" .
            "30 Riwayat Transaksi Terakhir (sebagai referensi detail/sumber uang):\n" .
            "- " . $transactionText . "\n\n" .
            "Gunakan data ini untuk menjawab pertanyaan pengguna. Jika bertanya darimana asal uang/pemasukan/pengeluaran, baca 'Riwayat Transaksi Terakhir' dan beritahukan deskripsi atau kategorinya. Jika pengguna bertanya tentang bulan tertentu, pastikan membaca 'Rincian Transaksi Bulanan' dan berikan data yang sesuai (jika 0 maka katakan tidak ada pengeluaran/pemasukan). Berikan saran yang bijak, ringkas, dan relevan dengan gaya bahasa santai namun profesional.\n" .
            "Pertanyaan pengguna: " . $message;

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $context]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
                
                // Parse markdown specifically bold since Gemini often responds with "**text**"
                $reply = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $reply);
                // Also parse *text* for italics
                $reply = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $reply);
                // Also replace newlines with <br>
                $reply = nl2br(e($reply));
                
                // Since we manually add HTML tags (like <strong> and <em> and <br>), 
                // we should decode the e() but ensure no XSS by only applying regex on escaped string.
                $escapedReply = e($data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.');
                $escapedReply = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escapedReply);
                $escapedReply = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $escapedReply);
                $escapedReply = nl2br($escapedReply);

                return response()->json(['reply' => $escapedReply]);
            }

            Log::error('Gemini API Error: ' . $response->body());
            return response()->json(['reply' => 'Maaf, terjadi kesalahan saat menghubungi server AI. Coba lagi nanti.'], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json(['reply' => 'Terjadi kesalahan internal. Coba lagi nanti.'], 500);
        }
    }
}
