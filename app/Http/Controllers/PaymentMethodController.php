<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods()->get();
        return view('payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'balance' => 'nullable|numeric',
        ]);
        $request->user()->paymentMethods()->create($validated);
        return redirect()->route('payment-methods.index')->with('success', 'Payment Method added.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== auth()->id()) abort(403);
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== auth()->id()) abort(403);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'balance' => 'nullable|numeric',
        ]);
        $paymentMethod->update($validated);
        return redirect()->route('payment-methods.index')->with('success', 'Payment Method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== auth()->id()) abort(403);
        try {
            $paymentMethod->delete();
            return redirect()->route('payment-methods.index')->with('success', 'Payment Method deleted.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('payment-methods.index')->with('error', 'Payment method cannot be deleted because it is still used in transactions.');
        }
    }
}
