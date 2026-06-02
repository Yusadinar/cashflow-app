<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
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

        foreach ($users as $user) {
            foreach ($categories as $category) {
                Category::updateOrCreate(
                    ['user_id' => $user->id, 'name' => $category['name']],
                    ['type' => $category['type']]
                );
            }
        }
    }
}
