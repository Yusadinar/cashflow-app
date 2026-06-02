<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $users = collect([User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
            ])]);
        }

        $paymentMethods = [
            'BCA',
            'Jago',
            'Bibit',
            'Seabank',
            'Gopay',
            'Shopeepay',
            'Dana',
            'BNI',
            'Saqu',
        ];

        foreach ($users as $user) {
            foreach ($paymentMethods as $method) {
                PaymentMethod::updateOrCreate([
                    'user_id' => $user->id,
                    'name' => $method,
                ]);
            }
        }
    }
}
