<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class UpdateCategoryCodes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            'Computer Supplies' => 'CS',
            'Janitorial' => 'JN',
            'Office' => 'OF',
        ];

        foreach ($codes as $name => $code) {
            Category::where('name', $name)->update(['code' => $code]);
        }

        // For any other categories without a code, generate one from first 2 letters
        $categoriesWithoutCode = Category::whereNull('code')->get();
        foreach ($categoriesWithoutCode as $category) {
            $code = strtoupper(substr($category->name, 0, 2));
            $category->update(['code' => $code]);
        }
    }
}
