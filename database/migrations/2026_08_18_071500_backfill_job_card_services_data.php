<?php

use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $services = Service::all()->keyBy('id');
        $staffIds = Staff::pluck('id')->toArray();

        $defaultAmounts = [
            'Hair' => 450,
            'Facial' => 1200,
            'Manicure' => 600,
            'Pedicure' => 700,
            'Waxing' => 850,
            'Bridal' => 4500,
            'Massage' => 2200,
            'Combo' => 1500,
            'Coloring' => 2800,
            'Beard' => 250,
        ];

        JobCard::chunk(50, function ($jobCards) use ($services, $staffIds, $defaultAmounts) {
            foreach ($jobCards as $jobCard) {
                // Ensure customer is synced
                if ($jobCard->customer_id && $jobCard->customers()->count() === 0) {
                    $jobCard->customers()->sync([$jobCard->customer_id]);
                }

                // If job card has no service items, create one from service_id
                if ($jobCard->serviceItems()->count() === 0) {
                    $service = $services->get($jobCard->service_id) ?? $services->first();
                    if ($service) {
                        $subcat = $jobCard->subcategory ?: ($service->subcategory ?: 'General');
                        $amount = $defaultAmounts[$subcat] ?? (($jobCard->id % 7 + 1) * 350);

                        $item = JobCardService::create([
                            'job_card_id' => $jobCard->id,
                            'service_id' => $service->id,
                            'subcategory' => $subcat,
                            'amount' => $amount,
                        ]);

                        $assignedStaff = $jobCard->staff_id ? [$jobCard->staff_id] : (!empty($staffIds) ? [$staffIds[$jobCard->id % count($staffIds)]] : []);
                        if (!empty($assignedStaff)) {
                            $item->staff()->sync($assignedStaff);
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
    }
};
