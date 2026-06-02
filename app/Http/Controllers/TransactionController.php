<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->transactions()->with(['category', 'paymentMethod']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        
        if ($category_id = $request->input('category_id')) {
            $query->where('category_id', $category_id);
        }

        $sort = $request->input('sort', 'transaction_date');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = ['transaction_date', 'amount', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (in_array($sort, $allowedSorts) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->latest('transaction_date');
        }

        $transactions = $query->paginate(20)->withQueryString();
        $categories = $request->user()->categories;

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = $request->user()->categories;
        $paymentMethods = $request->user()->paymentMethods;
        return view('transactions.create', compact('categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);
        
        $category = \App\Models\Category::findOrFail($validated['category_id']);
        if ($category->user_id !== auth()->id()) abort(403);
        if ($category->type !== $validated['type']) {
            return back()->withErrors(['category_id' => 'Category type does not match transaction type.'])->withInput();
        }
        
        $paymentMethod = \App\Models\PaymentMethod::findOrFail($validated['payment_method_id']);
        if ($paymentMethod->user_id !== auth()->id()) abort(403);

        $request->user()->transactions()->create($validated);
        return redirect()->route('transactions.index')->with('success', 'Transaction added.');
    }

    public function edit(Transaction $transaction, Request $request)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        $categories = $request->user()->categories;
        $paymentMethods = $request->user()->paymentMethods;
        return view('transactions.edit', compact('transaction', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        $category = \App\Models\Category::findOrFail($validated['category_id']);
        if ($category->user_id !== auth()->id()) abort(403);
        if ($category->type !== $validated['type']) {
            return back()->withErrors(['category_id' => 'Category type does not match transaction type.'])->withInput();
        }
        
        $paymentMethod = \App\Models\PaymentMethod::findOrFail($validated['payment_method_id']);
        if ($paymentMethod->user_id !== auth()->id()) abort(403);

        $transaction->update($validated);
        return redirect()->route('transactions.index')->with('success', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
