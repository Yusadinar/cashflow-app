<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->transfers()->with(['fromPaymentMethod', 'toPaymentMethod']);

        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $sort = $request->input('sort', 'transfer_date');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = ['transfer_date', 'amount', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (in_array($sort, $allowedSorts) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('transfer_date');
        }

        $transfers = $query->paginate(20)->withQueryString();

        return view('transfers.index', compact('transfers'));
    }

    public function create(Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods;
        return view('transfers.create', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_payment_method_id' => 'required|exists:payment_methods,id|different:to_payment_method_id',
            'to_payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transfer_date' => 'required|date',
        ]);

        $fromPm = \App\Models\PaymentMethod::findOrFail($validated['from_payment_method_id']);
        $toPm = \App\Models\PaymentMethod::findOrFail($validated['to_payment_method_id']);
        
        if ($fromPm->user_id !== auth()->id() || $toPm->user_id !== auth()->id()) {
            abort(403);
        }

        $request->user()->transfers()->create($validated);
        return redirect()->route('transfers.index')->with('success', 'Transfer recorded successfully.');
    }

    public function destroy(Transfer $transfer)
    {
        if ($transfer->user_id !== auth()->id()) abort(403);
        $transfer->delete();
        return redirect()->route('transfers.index')->with('success', 'Transfer deleted.');
    }
}
