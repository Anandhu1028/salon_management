<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaintTypes = [
            [
                'name' => 'Behavior',
                'icon' => 'bi-person-exclamation',
                'color' => '#EF4444',
                'description' => 'Inappropriate or unprofessional behavior',
            ],
            [
                'name' => 'Attendance',
                'icon' => 'bi-clock-history',
                'color' => '#3B82F6',
                'description' => 'Attendance or punctuality issues',
            ],
            [
                'name' => 'Workplace',
                'icon' => 'bi-building',
                'color' => '#8B5CF6',
                'description' => 'Workplace environment or safety concerns',
            ],
            [
                'name' => 'Conduct',
                'icon' => 'bi-exclamation-circle',
                'color' => '#F59E0B',
                'description' => 'Code of conduct violation',
            ],
            [
                'name' => 'Policy Violation',
                'icon' => 'bi-file-text',
                'color' => '#EC4899',
                'description' => 'Violation of company policy',
            ],
            [
                'name' => 'Other',
                'icon' => 'bi-box',
                'color' => '#6366F1',
                'description' => 'Other complaint types',
            ],
        ];

        DB::table('complaint_types')->insert($complaintTypes);
    }
}
