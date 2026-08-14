<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\JobCard;
use App\Models\Product;
use App\Models\Service;
use App\Models\Staff;
use App\Support\ServiceIconResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        JobCard::truncate();
        Product::truncate();
        Service::truncate();
        Customer::truncate();
        Staff::truncate();

        Schema::enableForeignKeyConstraints();

        $staff = [
            ['name' => 'Anandhu K', 'email' => 'anandhu@gmail.com', 'mobile_number' => '97463274402', 'status' => 'active'],
            ['name' => 'Priya Sharma', 'email' => 'priya.sharma@salonpro.com', 'mobile_number' => '9876543210', 'status' => 'active'],
            ['name' => 'Rahul Menon', 'email' => 'rahul.m@salonpro.com', 'mobile_number' => '9123456780', 'status' => 'active'],
            ['name' => 'Sneha Patel', 'email' => 'sneha.p@salonpro.com', 'mobile_number' => '9988776655', 'status' => 'active'],
            ['name' => 'Arjun Nair', 'email' => 'arjun.n@salonpro.com', 'mobile_number' => '9012345678', 'status' => 'inactive'],
            ['name' => 'Meera Thomas', 'email' => null, 'mobile_number' => '8899776655', 'status' => 'active'],
            ['name' => 'Divya Krishnan', 'email' => 'divya.k@salonpro.com', 'mobile_number' => '8765432109', 'status' => 'active'],
            ['name' => 'Vikram Singh', 'email' => 'vikram.s@salonpro.com', 'mobile_number' => '8654321098', 'status' => 'active'],
            ['name' => 'Nisha Gupta', 'email' => 'nisha.g@salonpro.com', 'mobile_number' => '8543210987', 'status' => 'inactive'],
            ['name' => 'Karan Desai', 'email' => 'karan.d@salonpro.com', 'mobile_number' => '8432109876', 'status' => 'active'],
            ['name' => 'Pooja Reddy', 'email' => 'pooja.r@salonpro.com', 'mobile_number' => '8321098765', 'status' => 'active'],
        ];

        $staffModels = [];
        foreach ($staff as $row) {
            $staffModels[] = Staff::create($row);
        }

        $customers = [
            ['name' => 'Aisha Khan', 'email' => 'aisha.khan@gmail.com', 'mobile_number' => '9812345678'],
            ['name' => 'David Joseph', 'email' => 'david.j@outlook.com', 'mobile_number' => '9823456789'],
            ['name' => 'Fatima Ali', 'email' => 'fatima.ali@gmail.com', 'mobile_number' => '9834567890'],
            ['name' => 'George Mathew', 'email' => null, 'mobile_number' => '9845678901'],
            ['name' => 'Hema Reddy', 'email' => 'hema.reddy@gmail.com', 'mobile_number' => '9856789012'],
            ['name' => 'Ibrahim Shah', 'email' => 'ibrahim.s@gmail.com', 'mobile_number' => '9867890123'],
            ['name' => 'Jyoti Verma', 'email' => 'jyoti.v@gmail.com', 'mobile_number' => '9878901234'],
            ['name' => 'Kevin D\'Souza', 'email' => 'kevin.d@gmail.com', 'mobile_number' => '9889012345'],
            ['name' => 'Lakshmi Iyer', 'email' => 'lakshmi.i@gmail.com', 'mobile_number' => '9890123456'],
            ['name' => 'Manoj Pillai', 'email' => 'manoj.p@gmail.com', 'mobile_number' => '9901234567'],
            ['name' => 'Neha Kapoor', 'email' => 'neha.k@gmail.com', 'mobile_number' => '9912345678'],
            ['name' => 'Rohit Saxena', 'email' => 'rohit.s@gmail.com', 'mobile_number' => '9923456789'],
        ];

        $customerModels = [];
        $customerDaysThisMonth = [1, 2, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14];
        foreach ($customers as $index => $row) {
            $createdAt = now()->copy()->startOfMonth()->addDays($customerDaysThisMonth[$index] - 1)->setTime(10 + ($index % 7), 0);
            $customerModels[] = Customer::create([...$row, 'created_at' => $createdAt, 'updated_at' => $createdAt]);
        }

        $services = [
            ['service_name' => 'Classic Haircut', 'category' => 'Hair', 'subcategory' => 'Cut', 'price' => 350.00, 'status' => 'active'],
            ['service_name' => 'Hair Coloring', 'category' => 'Hair', 'subcategory' => 'Color', 'price' => 2500.00, 'status' => 'active'],
            ['service_name' => 'Keratin Treatment', 'category' => 'Hair', 'subcategory' => 'Treatment', 'price' => 4500.00, 'status' => 'active'],
            ['service_name' => 'Deep Cleansing Facial', 'category' => 'Skin', 'subcategory' => 'Facial', 'price' => 1200.00, 'status' => 'active'],
            ['service_name' => 'Bridal Makeup', 'category' => 'Makeup', 'subcategory' => 'Bridal', 'price' => 8000.00, 'status' => 'active'],
            ['service_name' => 'Manicure & Pedicure', 'category' => 'Nails', 'subcategory' => 'Combo', 'price' => 900.00, 'status' => 'active'],
            ['service_name' => 'Beard Trim & Shape', 'category' => 'Grooming', 'subcategory' => 'Beard', 'price' => 200.00, 'status' => 'active'],
            ['service_name' => 'Full Body Wax', 'category' => 'Skin', 'subcategory' => 'Waxing', 'price' => 1800.00, 'status' => 'inactive'],
            ['service_name' => 'Head Massage', 'category' => 'Spa', 'subcategory' => 'Massage', 'price' => 600.00, 'status' => 'active'],
            ['service_name' => 'Threading — Eyebrows', 'category' => 'Grooming', 'subcategory' => 'Threading', 'price' => 150.00, 'status' => 'active'],
            ['service_name' => 'Party Makeup', 'category' => 'Makeup', 'subcategory' => 'Party', 'price' => 3500.00, 'status' => 'active'],
            ['service_name' => 'Gel Manicure', 'category' => 'Nails', 'subcategory' => 'Manicure', 'price' => 1100.00, 'status' => 'active'],
        ];

        $serviceModels = [];
        foreach ($services as $row) {
            $resolved = ServiceIconResolver::resolve(
                $row['service_name'],
                $row['category'],
                $row['subcategory'] ?? null
            );

            $serviceModels[] = Service::create(array_merge($row, [
                'icon' => $resolved['primary'],
            ]));
        }

        $products = [
            ['product_name' => 'Argan Oil Shampoo', 'category' => 'Hair Care', 'subcategory' => 'Shampoo', 'price' => 649.00, 'status' => 'active'],
            ['product_name' => 'Hydrating Conditioner', 'category' => 'Hair Care', 'subcategory' => 'Conditioner', 'price' => 599.00, 'status' => 'active'],
            ['product_name' => 'Heat Protection Spray', 'category' => 'Hair Care', 'subcategory' => 'Styling', 'price' => 799.00, 'status' => 'active'],
            ['product_name' => 'Vitamin C Serum', 'category' => 'Skin Care', 'subcategory' => 'Serum', 'price' => 1299.00, 'status' => 'active'],
            ['product_name' => 'Sunscreen SPF 50', 'category' => 'Skin Care', 'subcategory' => 'Sun Care', 'price' => 549.00, 'status' => 'active'],
            ['product_name' => 'Matte Lipstick — Rose', 'category' => 'Makeup', 'subcategory' => 'Lips', 'price' => 499.00, 'status' => 'active'],
            ['product_name' => 'Nail Polish Remover', 'category' => 'Nails', 'subcategory' => 'Remover', 'price' => 149.00, 'status' => 'active'],
            ['product_name' => 'Beard Growth Oil', 'category' => 'Grooming', 'subcategory' => 'Beard Care', 'price' => 399.00, 'status' => 'active'],
            ['product_name' => 'Dry Shampoo', 'category' => 'Hair Care', 'subcategory' => 'Shampoo', 'price' => 449.00, 'status' => 'inactive'],
            ['product_name' => 'Face Mask Pack', 'category' => 'Skin Care', 'subcategory' => 'Mask', 'price' => 299.00, 'status' => 'active'],
            ['product_name' => 'Hair Serum', 'category' => 'Hair Care', 'subcategory' => 'Serum', 'price' => 899.00, 'status' => 'active'],
            ['product_name' => 'Cuticle Oil', 'category' => 'Nails', 'subcategory' => 'Care', 'price' => 249.00, 'status' => 'active'],
        ];

        foreach ($products as $row) {
            Product::create($row);
        }

        $activeStaff = collect($staffModels)->where('status', 'active')->values();

        $jobCards = [
            ['job_card_name' => 'Haircut — Aisha', 'customer_index' => 0, 'service_index' => 0, 'subcategory' => 'Cut', 'status' => 'completed'],
            ['job_card_name' => 'Bridal Trial — Hema', 'customer_index' => 4, 'service_index' => 4, 'subcategory' => 'Bridal', 'status' => 'in_progress'],
            ['job_card_name' => 'Keratin — David', 'customer_index' => 1, 'service_index' => 2, 'subcategory' => 'Treatment', 'status' => 'pending'],
            ['job_card_name' => 'Facial — Fatima', 'customer_index' => 2, 'service_index' => 3, 'subcategory' => 'Facial', 'status' => 'completed'],
            ['job_card_name' => 'Color — Jyoti', 'customer_index' => 6, 'service_index' => 1, 'subcategory' => 'Color', 'status' => 'in_progress'],
            ['job_card_name' => 'Mani-Pedi — Lakshmi', 'customer_index' => 8, 'service_index' => 5, 'subcategory' => 'Combo', 'status' => 'pending'],
            ['job_card_name' => 'Beard Groom — Manoj', 'customer_index' => 9, 'service_index' => 6, 'subcategory' => 'Beard', 'status' => 'completed'],
            ['job_card_name' => 'Waxing — Kevin', 'customer_index' => 7, 'service_index' => 7, 'subcategory' => 'Waxing', 'status' => 'cancelled'],
            ['job_card_name' => 'Massage — Neha', 'customer_index' => 10, 'service_index' => 8, 'subcategory' => 'Massage', 'status' => 'pending'],
            ['job_card_name' => 'Threading — Rohit', 'customer_index' => 11, 'service_index' => 9, 'subcategory' => 'Threading', 'status' => 'completed'],
            ['job_card_name' => 'Party Makeup — Aisha', 'customer_index' => 0, 'service_index' => 10, 'subcategory' => 'Party', 'status' => 'in_progress'],
            ['job_card_name' => 'Gel Manicure — David', 'customer_index' => 1, 'service_index' => 11, 'subcategory' => 'Manicure', 'status' => 'pending'],
        ];

        foreach ($jobCards as $index => $row) {
            $createdAt = now()->copy()->subDays($index % 7)->setTime(9 + ($index % 8), 0);

            JobCard::create([
                'job_card_name' => $row['job_card_name'],
                'customer_id' => $customerModels[$row['customer_index']]->id,
                'service_id' => $serviceModels[$row['service_index']]->id,
                'staff_id' => $activeStaff[$index % $activeStaff->count()]->id,
                'subcategory' => $row['subcategory'],
                'status' => $row['status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // A balanced 30-day schedule gives the dashboard meaningful, linked staff metrics.
        $statuses = ['completed', 'completed', 'completed', 'completed', 'in_progress', 'pending', 'cancelled'];
        foreach (range(0, 89) as $index) {
            $service = $serviceModels[$index % count($serviceModels)];
            $createdAt = now()->copy()
                ->subDays(29 - intdiv($index, 3))
                ->setTime(9 + ($index % 9), ($index * 10) % 60);

            JobCard::create([
                'job_card_name' => sprintf('%s Appointment %03d', $service->service_name, $index + 1),
                'customer_id' => $customerModels[$index % count($customerModels)]->id,
                'service_id' => $service->id,
                'staff_id' => $activeStaff[$index % $activeStaff->count()]->id,
                'subcategory' => $service->subcategory,
                'status' => $statuses[$index % count($statuses)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHour(),
            ]);
        }
    }
}
