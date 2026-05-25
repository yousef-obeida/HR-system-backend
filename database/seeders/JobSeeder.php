<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Software Engineer',
                'description' => 'We are looking for a skilled Software Engineer to join our backend team.',
                'requirments' => 'Experience with PHP, Laravel, and MySQL.',
                'status' => 'open',
                'Location' => 'remote',
                'salary' => 80000,
            ],
            [
                'title' => 'HR Specialist',
                'description' => 'Manage recruitment pipelines and employee relations.',
                'requirments' => 'Excellent communication and organizational skills.',
                'status' => 'open',
                'Location' => 'hybrid',
                'salary' => 60000,
            ],
            [
                'title' => 'Product Designer',
                'description' => 'Design user-friendly interfaces and experiences.',
                'requirments' => 'Proficiency in Figma and modern design principles.',
                'status' => 'open',
                'Location' => 'onsite',
                'salary' => 75000,
            ],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }
    }
}
