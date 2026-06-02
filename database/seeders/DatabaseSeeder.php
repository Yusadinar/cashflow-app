<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@cashflow.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
            ]
        );

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

        foreach ($categories as $cat) {
            $user->categories()->firstOrCreate(['name' => $cat['name']], ['type' => $cat['type']]);
        }

        $paymentMethods = [
            'BCA', 'Jago', 'Seabank', 'Gopay', 'Bibit', 'Dana', 'BNI', 'Saqu', 'Shopeepay'
        ];

        foreach ($paymentMethods as $pm) {
            $user->paymentMethods()->firstOrCreate(['name' => $pm]);
        }

        $user->transactions()->create([
            'category_id' => $user->categories()->where('name', 'Salary')->first()->id,
            'payment_method_id' => $user->paymentMethods()->where('name', 'BCA')->first()->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Monthly salary',
            'transaction_date' => now()->subDays(25),
        ]);

        $user->transactions()->create([
            'category_id' => $user->categories()->where('name', 'Food')->first()->id,
            'payment_method_id' => $user->paymentMethods()->where('name', 'Gopay')->first()->id,
            'type' => 'expense',
            'amount' => 150000,
            'description' => 'Lunch',
            'transaction_date' => now()->subDays(10),
        ]);
    }
}
