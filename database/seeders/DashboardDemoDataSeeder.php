<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\Service;
use App\Models\Staff;
use App\Support\ServiceIconResolver;
use Illuminate\Database\Seeder;

class DashboardDemoDataSeeder extends Seeder
{
    /**
     * Add realistic, repeat-safe records for a populated dashboard.
     */
    public function run(): void
    {
        $now = now();

        $staffList = collect([
            ['name' => 'Anjali Nair', 'mobile_country_code' => '+91', 'mobile_number' => '9001001001', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9001001001', 'status' => 'active'],
            ['name' => 'Rohan Varma', 'mobile_country_code' => '+91', 'mobile_number' => '9001001002', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9001001002', 'status' => 'active'],
            ['name' => 'Kavya Iyer', 'mobile_country_code' => '+91', 'mobile_number' => '9001001003', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9001001003', 'status' => 'active'],
            ['name' => 'Sanjay Kumar', 'mobile_country_code' => '+91', 'mobile_number' => '9001001004', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9001001004', 'status' => 'inactive'],
        ])->map(function ($row) {
            return Staff::firstOrCreate(['name' => $row['name']], $row);
        });
        $activeStaff = $staffList->where('status', 'active')->values();

        $customerRows = [
            ['name' => 'Nandita Bose', 'mobile_country_code' => '+91', 'mobile_number' => '9002001001', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001001'],
            ['name' => 'Aditya Rao', 'mobile_country_code' => '+91', 'mobile_number' => '9002001002', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001002'],
            ['name' => 'Sana Mirza', 'mobile_country_code' => '+91', 'mobile_number' => '9002001003', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001003'],
            ['name' => 'Vivek Nambiar', 'mobile_country_code' => '+91', 'mobile_number' => '9002001004', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001004'],
            ['name' => 'Ishita Das', 'mobile_country_code' => '+91', 'mobile_number' => '9002001005', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001005'],
            ['name' => 'Harish Babu', 'mobile_country_code' => '+91', 'mobile_number' => '9002001006', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001006'],
            ['name' => 'Riya Choudhary', 'mobile_country_code' => '+91', 'mobile_number' => '9002001007', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001007'],
            ['name' => 'Joel Fernandes', 'mobile_country_code' => '+91', 'mobile_number' => '9002001008', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001008'],
            ['name' => 'Tanvi Kulkarni', 'mobile_country_code' => '+91', 'mobile_number' => '9002001009', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001009'],
            ['name' => 'Farhan Siddiqui', 'mobile_country_code' => '+91', 'mobile_number' => '9002001010', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9002001010'],
        ];

        $customers = collect($customerRows)->map(function (array $row, int $index) use ($now) {
            $customer = Customer::firstOrCreate(['name' => $row['name']], $row);
            if ($customer->wasRecentlyCreated) {
                $customer->forceFill(['created_at' => $now->copy()->subDays(155 - ($index * 11))])->saveQuietly();
            }

            return $customer;
        })->values();

        $serviceRows = [
            ['service_name' => 'Hair Spa Ritual', 'category' => 'Hair', 'subcategory' => 'Spa', 'amount' => 1400.00, 'status' => 'active'],
            ['service_name' => 'Global Hair Colour', 'category' => 'Hair', 'subcategory' => 'Colour', 'amount' => 4200.00, 'status' => 'active'],
            ['service_name' => 'Hydra Glow Facial', 'category' => 'Skin', 'subcategory' => 'Facial', 'amount' => 1800.00, 'status' => 'active'],
            ['service_name' => 'Luxury Pedicure', 'category' => 'Nails', 'subcategory' => 'Pedicure', 'amount' => 1200.00, 'status' => 'active'],
            ['service_name' => 'Relaxing Back Massage', 'category' => 'Spa', 'subcategory' => 'Massage', 'amount' => 1500.00, 'status' => 'active'],
            ['service_name' => 'Groom Package', 'category' => 'Grooming', 'subcategory' => 'Package', 'amount' => 1100.00, 'status' => 'active'],
        ];

        $serviceAmounts = [];
        $services = collect($serviceRows)->map(function (array $row) use (&$serviceAmounts) {
            $amt = $row['amount'];
            unset($row['amount']);
            $icon = ServiceIconResolver::resolve($row['service_name'], $row['category'], $row['subcategory']);

            $svc = Service::firstOrCreate(
                ['service_name' => $row['service_name']],
                [...$row, 'icon' => $icon['primary']]
            );
            $serviceAmounts[$svc->id] = $amt;
            return $svc;
        })->values();

        foreach ([
            ['product_name' => 'Repair & Shine Hair Mask', 'category' => 'Hair Care', 'subcategory' => 'Treatment', 'price' => 1099, 'status' => 'active'],
            ['product_name' => 'Daily Glow Moisturizer', 'category' => 'Skin Care', 'subcategory' => 'Moisturizer', 'price' => 899, 'status' => 'active'],
            ['product_name' => 'Professional Makeup Setting Spray', 'category' => 'Makeup', 'subcategory' => 'Face', 'price' => 749, 'status' => 'active'],
            ['product_name' => 'Nourishing Beard Balm', 'category' => 'Grooming', 'subcategory' => 'Beard Care', 'price' => 429, 'status' => 'active'],
            ['product_name' => 'Salon Foot Cream', 'category' => 'Nails', 'subcategory' => 'Care', 'price' => 349, 'status' => 'active'],
            ['product_name' => 'Clarifying Scalp Scrub', 'category' => 'Hair Care', 'subcategory' => 'Scalp Care', 'price' => 699, 'status' => 'active'],
        ] as $row) {
            Product::firstOrCreate(['product_name' => $row['product_name']], $row);
        }

        $paymentType = PaymentType::where('is_active', true)->first();
        if (!$paymentType) {
            $paymentType = PaymentType::create(['name' => 'Cash', 'is_active' => true]);
        }

        foreach (range(0, 71) as $index) {
            $customer = $customers[$index % $customers->count()];
            $service = $services[$index % $services->count()];
            $staffMember = $activeStaff[$index % $activeStaff->count()];
            $date = $now->copy()->subDays(140 - ($index * 2));
            $name = sprintf('%s Visit %02d', $service->service_name, $index + 1);

            $jobCard = JobCard::firstOrCreate(
                ['job_card_name' => $name],
                [
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'staff_id' => $staffMember->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'status' => 'completed',
                    'discount_amount' => 0,
                ]
            );

            if ($jobCard->wasRecentlyCreated) {
                $jobCard->forceFill(['created_at' => $date, 'updated_at' => $date->copy()->addHours(2)])->saveQuietly();
                $jobCard->customers()->sync([$customer->id]);
                $jobCard->staff()->sync([$staffMember->id]);

                $svcItem = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $service->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'amount' => $serviceAmounts[$service->id] ?? 1000.00,
                    'payment_type_id' => $paymentType->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                $svcItem->staff()->sync([$staffMember->id]);
            }
        }

        foreach (range(0, 11) as $index) {
            $customer = $customers[$index % $customers->count()];
            $service = $services[($index + 2) % $services->count()];
            $staffMember = $activeStaff[($index + 1) % $activeStaff->count()];
            $status = ['completed', 'completed', 'pending', 'in_progress', 'completed', 'cancelled'][$index % 6];
            $date = $now->copy()->startOfDay()->addHours(9 + ($index % 8));
            $name = sprintf('Today %s %02d', $service->service_name, $index + 1);

            $jobCard = JobCard::firstOrCreate(
                ['job_card_name' => $name],
                [
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'staff_id' => $staffMember->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'status' => $status,
                    'discount_amount' => 0,
                ]
            );

            if ($jobCard->wasRecentlyCreated) {
                $jobCard->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
                $jobCard->customers()->sync([$customer->id]);
                $jobCard->staff()->sync([$staffMember->id]);

                $svcItem = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $service->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'amount' => $serviceAmounts[$service->id] ?? 1000.00,
                    'payment_type_id' => $paymentType->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                $svcItem->staff()->sync([$staffMember->id]);
            }
        }
    }
}
