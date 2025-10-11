<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            [
                'title' => 'Server Downtime Alert',
                'description' => 'The main web server was down for 2 hours due to a network issue.',
                'status' => 'resolved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'New Feature Deployment',
                'description' => 'The new analytics module was successfully deployed to production.',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Login Bug Reported',
                'description' => 'Users reported intermittent login failures during peak hours.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Database Optimization',
                'description' => 'Indexes were added to improve query performance on large tables.',
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Report::insert($reports);
    }
}
