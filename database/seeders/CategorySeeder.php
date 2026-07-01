<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Feature Requests', 'color' => '#3b82f6'],
            ['name' => 'Bug Fixes', 'color' => '#ef4444'],
            ['name' => 'Improvements', 'color' => '#22c55e'],
            ['name' => 'Technical Debt', 'color' => '#f97316'],
            ['name' => 'Reports/Analytics', 'color' => '#8b5cf6'],
            ['name' => 'User Management/Access', 'color' => '#06b6d4'],
            ['name' => 'Documentation', 'color' => '#64748b'],
            ['name' => 'Testing/Quality Assurance', 'color' => '#14b8a6'],
            ['name' => 'Deployment/Infrastructure', 'color' => '#eab308'],
            ['name' => 'Customer/Client Requests', 'color' => '#ec4899'],
            ['name' => 'Research/Exploration', 'color' => '#a855f7'],
            ['name' => 'Meetings/Discussions', 'color' => '#0ea5e9'],
            ['name' => 'Production Support', 'color' => '#dc2626'],
            ['name' => 'Code Review', 'color' => '#4f46e5'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'color' => $category['color']]
            );
        }
    }
}
