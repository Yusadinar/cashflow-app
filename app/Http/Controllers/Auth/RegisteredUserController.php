<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto-seed default categories for the new user
        $categories = [
            ['name' => 'Salary', 'type' => 'income'],
            ['name' => 'Freelance', 'type' => 'income'],
            ['name' => 'Investment', 'type' => 'income'],
            ['name' => 'Other Income', 'type' => 'income'],
            ['name' => 'Rent', 'type' => 'expense'],
            ['name' => 'Food', 'type' => 'expense'],
            ['name' => 'Transport', 'type' => 'expense'],
            ['name' => 'Utilities', 'type' => 'expense'],
            ['name' => 'Entertainment', 'type' => 'expense'],
            ['name' => 'Healthcare', 'type' => 'expense'],
            ['name' => 'Shopping', 'type' => 'expense'],
            ['name' => 'Other Expense', 'type' => 'expense'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create([
                'user_id' => $user->id,
                'name' => $category['name'],
                'type' => $category['type'],
            ]);
        }

        // Auto-seed default payment methods for the new user
        $paymentMethods = ['BCA', 'Jago', 'Bibit', 'Seabank', 'Gopay', 'Shopeepay', 'Dana', 'BNI', 'Saqu'];
        
        foreach ($paymentMethods as $method) {
            \App\Models\PaymentMethod::create([
                'user_id' => $user->id,
                'name' => $method,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
