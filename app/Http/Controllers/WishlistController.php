<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $wishlists = $user->wishlists()->with('paymentMethod')->latest()->get();

        $paymentMethods = $user->paymentMethods;
        $balances = [];
        
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

        foreach ($paymentMethods as $pm) {
            $pmIncome = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'income')->sum('total');
            $pmExpense = $transactionSums->where('payment_method_id', $pm->id)->where('type', 'expense')->sum('total');
            $transfersIn = $transferSumsIn->where('to_payment_method_id', $pm->id)->sum('total');
            $transfersOut = $transferSumsOut->where('from_payment_method_id', $pm->id)->sum('total');

            $balances[$pm->id] = $pm->balance + $pmIncome - $pmExpense + $transfersIn - $transfersOut;
        }

        return view('wishlists.index', compact('wishlists', 'balances'));
    }

    public function create(Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods;
        return view('wishlists.create', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        if ($validated['payment_method_id']) {
            $pm = PaymentMethod::findOrFail($validated['payment_method_id']);
            if ($pm->user_id !== auth()->id()) abort(403);
        }

        $request->user()->wishlists()->create($validated);
        return redirect()->route('wishlists.index')->with('success', 'Wishlist item added successfully.');
    }

    public function edit(Request $request, Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) abort(403);
        $paymentMethods = $request->user()->paymentMethods;
        return view('wishlists.edit', compact('wishlist', 'paymentMethods'));
    }

    public function update(Request $request, Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        if ($validated['payment_method_id']) {
            $pm = PaymentMethod::findOrFail($validated['payment_method_id']);
            if ($pm->user_id !== auth()->id()) abort(403);
        }

        $wishlist->update($validated);
        return redirect()->route('wishlists.index')->with('success', 'Wishlist item updated successfully.');
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== auth()->id()) abort(403);
        $wishlist->delete();
        return redirect()->route('wishlists.index')->with('success', 'Wishlist item deleted.');
    }
}
