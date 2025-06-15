<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Survey;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $survey = Survey::create([
            'title' => 'Customer Satisfaction 2025',
            'description' => 'A survey about customers’ experience with our services.',
            'status' => 'active',
        ]);

        // Create questions using the relationship
        $survey->questions()->createMany([
            [
                'type' => 'text',
                'question_text' => 'How would you rate our service?',
            ],
            [
                'type' => 'multiple_choice',
                'question_text' => 'What are the reasons you choose our service?',
            ],
        ]);

        $survey2 = Survey::create([
            'title' => 'Employee Feedback Survey',
            'description' => 'Anonymous survey about the work environment.',
        ]);

        $survey2->questions()->createMany([
            [
                'type' => 'text',
                'question_text' => 'How would you describe the work environment?',
            ],
            [
                'type' => 'multiple_choice',
                'question_text' => 'Are you satisfied with the benefits?',
            ],
        ]);
    }
}
