<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\JobCard;
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

        foreach ([
            ['name' => 'Anjali Nair', 'email' => 'anjali.nair@salonpro.test', 'mobile_number' => '9001001001', 'status' => 'active'],
            ['name' => 'Rohan Varma', 'email' => 'rohan.varma@salonpro.test', 'mobile_number' => '9001001002', 'status' => 'active'],
            ['name' => 'Kavya Iyer', 'email' => 'kavya.iyer@salonpro.test', 'mobile_number' => '9001001003', 'status' => 'active'],
            ['name' => 'Sanjay Kumar', 'email' => 'sanjay.kumar@salonpro.test', 'mobile_number' => '9001001004', 'status' => 'inactive'],
        ] as $row) {
            Staff::firstOrCreate(['email' => $row['email']], $row);
        }

        $customerRows = [
            ['name' => 'Nandita Bose', 'email' => 'nandita.bose@example.test', 'mobile_number' => '9002001001'],
            ['name' => 'Aditya Rao', 'email' => 'aditya.rao@example.test', 'mobile_number' => '9002001002'],
            ['name' => 'Sana Mirza', 'email' => 'sana.mirza@example.test', 'mobile_number' => '9002001003'],
            ['name' => 'Vivek Nambiar', 'email' => 'vivek.nambiar@example.test', 'mobile_number' => '9002001004'],
            ['name' => 'Ishita Das', 'email' => 'ishita.das@example.test', 'mobile_number' => '9002001005'],
            ['name' => 'Harish Babu', 'email' => 'harish.babu@example.test', 'mobile_number' => '9002001006'],
            ['name' => 'Riya Choudhary', 'email' => 'riya.choudhary@example.test', 'mobile_number' => '9002001007'],
            ['name' => 'Joel Fernandes', 'email' => 'joel.fernandes@example.test', 'mobile_number' => '9002001008'],
            ['name' => 'Tanvi Kulkarni', 'email' => 'tanvi.kulkarni@example.test', 'mobile_number' => '9002001009'],
            ['name' => 'Farhan Siddiqui', 'email' => 'farhan.siddiqui@example.test', 'mobile_number' => '9002001010'],
        ];

        $customers = collect($customerRows)->map(function (array $row, int $index) use ($now) {
            $customer = Customer::firstOrCreate(['email' => $row['email']], $row);
            if ($customer->wasRecentlyCreated) {
                $customer->forceFill(['created_at' => $now->copy()->subDays(155 - ($index * 11))])->saveQuietly();
            }

            return $customer;
        })->values();

        $serviceRows = [
            ['service_name' => 'Hair Spa Ritual', 'category' => 'Hair', 'subcategory' => 'Spa', 'price' => 1400, 'status' => 'active'],
            ['service_name' => 'Global Hair Colour', 'category' => 'Hair', 'subcategory' => 'Colour', 'price' => 4200, 'status' => 'active'],
            ['service_name' => 'Hydra Glow Facial', 'category' => 'Skin', 'subcategory' => 'Facial', 'price' => 1800, 'status' => 'active'],
            ['service_name' => 'Luxury Pedicure', 'category' => 'Nails', 'subcategory' => 'Pedicure', 'price' => 1200, 'status' => 'active'],
            ['service_name' => 'Relaxing Back Massage', 'category' => 'Spa', 'subcategory' => 'Massage', 'price' => 1500, 'status' => 'active'],
            ['service_name' => 'Groom Package', 'category' => 'Grooming', 'subcategory' => 'Package', 'price' => 1100, 'status' => 'active'],
        ];

        $services = collect($serviceRows)->map(function (array $row) {
            $icon = ServiceIconResolver::resolve($row['service_name'], $row['category'], $row['subcategory']);

            return Service::firstOrCreate(
                ['service_name' => $row['service_name']],
                [...$row, 'icon' => $icon['primary']]
            );
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

        foreach (range(0, 71) as $index) {
            $customer = $customers[$index % $customers->count()];
            $service = $services[$index % $services->count()];
            $date = $now->copy()->subDays(140 - ($index * 2));
            $name = sprintf('%s Visit %02d', $service->service_name, $index + 1);

            $jobCard = JobCard::firstOrCreate(
                ['job_card_name' => $name],
                [
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'status' => 'completed',
                ]
            );

            if ($jobCard->wasRecentlyCreated) {
                $jobCard->forceFill(['created_at' => $date, 'updated_at' => $date->copy()->addHours(2)])->saveQuietly();
            }
        }

        foreach (range(0, 11) as $index) {
            $customer = $customers[$index % $customers->count()];
            $service = $services[($index + 2) % $services->count()];
            $status = ['completed', 'completed', 'pending', 'in_progress', 'completed', 'cancelled'][$index % 6];
            $date = $now->copy()->startOfDay()->addHours(9 + ($index % 8));
            $name = sprintf('Today %s %02d', $service->service_name, $index + 1);

            $jobCard = JobCard::firstOrCreate(
                ['job_card_name' => $name],
                [
                    'customer_id' => $customer->id,
                    'service_id' => $service->id,
                    'subcategory' => $service->subcategory ?? $service->category,
                    'status' => $status,
                ]
            );

            if ($jobCard->wasRecentlyCreated) {
                $jobCard->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
            }
        }
    }
}
