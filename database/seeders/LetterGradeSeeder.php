<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LetterGrade;

class LetterGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing letter grades
        LetterGrade::truncate();
        
        // Default letter grades with standard scale
        $letterGrades = [
            ['letter' => 'A', 'bobot' => 4.00, 'min_score' => 80, 'max_score' => 100, 'order' => 7, 'is_active' => true],
            ['letter' => 'B+', 'bobot' => 3.50, 'min_score' => 75, 'max_score' => 79, 'order' => 6, 'is_active' => true],
            ['letter' => 'B', 'bobot' => 3.00, 'min_score' => 70, 'max_score' => 74, 'order' => 5, 'is_active' => true],
            ['letter' => 'C+', 'bobot' => 2.50, 'min_score' => 65, 'max_score' => 69, 'order' => 4, 'is_active' => true],
            ['letter' => 'C', 'bobot' => 2.00, 'min_score' => 60, 'max_score' => 64, 'order' => 3, 'is_active' => true],
            ['letter' => 'D', 'bobot' => 1.00, 'min_score' => 50, 'max_score' => 59, 'order' => 2, 'is_active' => true],
            ['letter' => 'E', 'bobot' => 0.00, 'min_score' => 0, 'max_score' => 49, 'order' => 1, 'is_active' => true],
        ];
        
        foreach ($letterGrades as $grade) {
            LetterGrade::create($grade);
        }
        
        $this->command->info('Letter grades seeded successfully!');
    }
}
