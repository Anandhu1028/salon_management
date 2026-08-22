<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Customer;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\MarketingActivity;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Support\ServiceIconResolver;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed a realistic, presentation-ready salon dataset.
     *
     * The data is intentionally fictional and is safe to use for demos,
     * screenshots and local development.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('job_card_service_staff')->truncate();
        DB::table('job_card_staff')->truncate();
        DB::table('job_card_customer')->truncate();
        DB::table('job_card_services')->truncate();
        JobCard::truncate();
        ProductPurchaseItem::truncate();
        ProductPurchase::truncate();
        Product::truncate();
        Service::truncate();
        StaffAttendance::truncate();
        Complaint::truncate();
        MarketingActivity::truncate();
        Customer::truncate();
        Staff::truncate();

        Schema::enableForeignKeyConstraints();

        $today = now()->startOfDay();

        // -----------------------------------------------------------------
        // 1. STAFF
        // -----------------------------------------------------------------
        $staffData = [
            ['name' => 'Meera Nair', 'mobile_country_code' => '+91', 'mobile_number' => '9000001001', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001001', 'status' => 'active'],
            ['name' => 'Arjun Menon', 'mobile_country_code' => '+91', 'mobile_number' => '9000001002', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001002', 'status' => 'active'],
            ['name' => 'Ananya Krishnan', 'mobile_country_code' => '+91', 'mobile_number' => '9000001003', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001003', 'status' => 'active'],
            ['name' => 'Rahul Varma', 'mobile_country_code' => '+91', 'mobile_number' => '9000001004', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001004', 'status' => 'active'],
            ['name' => 'Diya Thomas', 'mobile_country_code' => '+91', 'mobile_number' => '9000001005', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001005', 'status' => 'active'],
            ['name' => 'Vishnu Raj', 'mobile_country_code' => '+91', 'mobile_number' => '9000001006', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001006', 'status' => 'active'],
            ['name' => 'Kavya Suresh', 'mobile_country_code' => '+91', 'mobile_number' => '9000001007', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001007', 'status' => 'active'],
            ['name' => 'Nikhil Das', 'mobile_country_code' => '+91', 'mobile_number' => '9000001008', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001008', 'status' => 'active'],
            ['name' => 'Isha Mathew', 'mobile_country_code' => '+91', 'mobile_number' => '9000001009', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001009', 'status' => 'active'],
            ['name' => 'Adarsh Kumar', 'mobile_country_code' => '+91', 'mobile_number' => '9000001010', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001010', 'status' => 'active'],
            ['name' => 'Neha Pillai', 'mobile_country_code' => '+91', 'mobile_number' => '9000001011', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001011', 'status' => 'active'],
            ['name' => 'Sanjay Babu', 'mobile_country_code' => '+91', 'mobile_number' => '9000001012', 'whatsapp_country_code' => '+91', 'whatsapp_number' => '9000001012', 'status' => 'inactive'],
        ];

        $staffModels = collect($staffData)->map(fn (array $row) => Staff::create($row))->values();
        $activeStaff = $staffModels->where('status', 'active')->values();

        // -----------------------------------------------------------------
        // 2. CUSTOMERS
        // -----------------------------------------------------------------
        $customerNames = [
            'Aarav Kapoor',
            'Aditi Menon',
            'Alina Joseph',
            'Amal Varghese',
            'Anjali Rao',
            'Arvind Nair',
            'Bhavana Iyer',
            'Catherine Thomas',
            'Devika Nambiar',
            'Farhan Ali',
            'Gauri Krishnan',
            'Harish Kumar',
            'Ishita Sharma',
            'Jithin Mathew',
            'Karthika Das',
            'Lakshmi Menon',
            'Manu George',
            'Maya Suresh',
            'Neeraj Pillai',
            'Nandana Raj',
            'Pooja Nair',
            'Rahul Joseph',
            'Rhea Kapoor',
            'Rohit Menon',
            'Sana Fathima',
            'Shreya Varma',
            'Siddharth Rao',
            'Sneha Thomas',
            'Vivek Nambiar',
            'Zoya Khan',
        ];

        $customerModels = collect($customerNames)->map(function (string $name, int $index) use ($today) {
            $createdAt = $today->copy()
                ->subDays(55 - min($index * 2, 54))
                ->setTime(9 + ($index % 8), ($index * 7) % 60);

            return Customer::create([
                'name' => $name,
                'mobile_country_code' => '+91',
                'mobile_number' => '900000' . str_pad((string) (2001 + $index), 4, '0', STR_PAD_LEFT),
                'whatsapp_country_code' => '+91',
                'whatsapp_number' => '900000' . str_pad((string) (2001 + $index), 4, '0', STR_PAD_LEFT),
                'status' => $index % 13 === 0 ? 'inactive' : 'active',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        })->values();

        // -----------------------------------------------------------------
        // 3. SERVICES
        // -----------------------------------------------------------------
        $serviceData = [
            ['service_name' => 'Signature Haircut', 'category' => 'Hair', 'subcategory' => 'Haircut', 'amount' => 450],
            ['service_name' => 'Hair Wash & Blow Dry', 'category' => 'Hair', 'subcategory' => 'Styling', 'amount' => 550],
            ['service_name' => 'Hair Colour - Global', 'category' => 'Hair', 'subcategory' => 'Colour', 'amount' => 2800],
            ['service_name' => 'Balayage Highlights', 'category' => 'Hair', 'subcategory' => 'Highlights', 'amount' => 4500],
            ['service_name' => 'Keratin Smoothing', 'category' => 'Hair', 'subcategory' => 'Treatment', 'amount' => 5200],
            ['service_name' => 'Hair Spa - Nourishing', 'category' => 'Hair', 'subcategory' => 'Hair Spa', 'amount' => 1600],
            ['service_name' => 'Classic Cleanup', 'category' => 'Skin', 'subcategory' => 'Cleanup', 'amount' => 650],
            ['service_name' => 'Hydra Glow Facial', 'category' => 'Skin', 'subcategory' => 'Facial', 'amount' => 1800],
            ['service_name' => 'Brightening Facial', 'category' => 'Skin', 'subcategory' => 'Facial', 'amount' => 1500],
            ['service_name' => 'Full Face Threading', 'category' => 'Grooming', 'subcategory' => 'Threading', 'amount' => 300],
            ['service_name' => 'Beard Trim & Styling', 'category' => 'Grooming', 'subcategory' => 'Beard', 'amount' => 300],
            ['service_name' => 'Premium Manicure', 'category' => 'Nails', 'subcategory' => 'Manicure', 'amount' => 900],
            ['service_name' => 'Classic Pedicure', 'category' => 'Nails', 'subcategory' => 'Pedicure', 'amount' => 1000],
            ['service_name' => 'Gel Nail Extension', 'category' => 'Nails', 'subcategory' => 'Nail Extension', 'amount' => 2200],
            ['service_name' => 'Head & Shoulder Massage', 'category' => 'Spa', 'subcategory' => 'Massage', 'amount' => 900],
            ['service_name' => 'Relaxing Back Massage', 'category' => 'Spa', 'subcategory' => 'Massage', 'amount' => 1500],
            ['service_name' => 'Party Makeup', 'category' => 'Makeup', 'subcategory' => 'Party Makeup', 'amount' => 3500],
            ['service_name' => 'Bridal Makeup', 'category' => 'Makeup', 'subcategory' => 'Bridal', 'amount' => 9500],
            ['service_name' => 'Full Body Waxing', 'category' => 'Skin', 'subcategory' => 'Waxing', 'amount' => 2200],
            ['service_name' => 'Bridal Hair & Makeup Package', 'category' => 'Packages', 'subcategory' => 'Bridal Package', 'amount' => 14500],
        ];

        $serviceModels = collect();
        $serviceAmounts = [];

        foreach ($serviceData as $row) {
            $amount = $row['amount'];
            unset($row['amount']);

            $resolved = ServiceIconResolver::resolve(
                $row['service_name'],
                $row['category'],
                $row['subcategory']
            );

            $service = Service::create([
                ...$row,
                'icon' => $resolved['primary'],
                'status' => 'active',
            ]);

            $serviceModels->push($service);
            $serviceAmounts[$service->id] = $amount;
        }

        // -----------------------------------------------------------------
        // 4. PRODUCTS
        // -----------------------------------------------------------------
        $productData = [
            ['product_name' => 'L’Oréal Professionnel Absolut Repair Shampoo', 'category' => 'Hair Care', 'subcategory' => 'Shampoo', 'price' => 1250],
            ['product_name' => 'L’Oréal Professionnel Absolut Repair Conditioner', 'category' => 'Hair Care', 'subcategory' => 'Conditioner', 'price' => 1350],
            ['product_name' => 'Moroccanoil Treatment', 'category' => 'Hair Care', 'subcategory' => 'Serum', 'price' => 2800],
            ['product_name' => 'Schwarzkopf Heat Protection Spray', 'category' => 'Hair Care', 'subcategory' => 'Styling', 'price' => 1100],
            ['product_name' => 'Wella Professionals Hair Mask', 'category' => 'Hair Care', 'subcategory' => 'Hair Mask', 'price' => 1450],
            ['product_name' => 'Olaplex No.3 Hair Perfector', 'category' => 'Hair Care', 'subcategory' => 'Treatment', 'price' => 3200],
            ['product_name' => 'Vitamin C Brightening Serum', 'category' => 'Skin Care', 'subcategory' => 'Serum', 'price' => 1499],
            ['product_name' => 'Hydrating Face Cleanser', 'category' => 'Skin Care', 'subcategory' => 'Cleanser', 'price' => 699],
            ['product_name' => 'SPF 50 PA+++ Sunscreen', 'category' => 'Skin Care', 'subcategory' => 'Sun Care', 'price' => 899],
            ['product_name' => 'Hyaluronic Acid Moisturizer', 'category' => 'Skin Care', 'subcategory' => 'Moisturizer', 'price' => 1099],
            ['product_name' => 'Clay Purifying Face Mask', 'category' => 'Skin Care', 'subcategory' => 'Mask', 'price' => 599],
            ['product_name' => 'Professional Matte Lipstick - Nude', 'category' => 'Makeup', 'subcategory' => 'Lips', 'price' => 799],
            ['product_name' => 'Makeup Setting Spray', 'category' => 'Makeup', 'subcategory' => 'Setting', 'price' => 999],
            ['product_name' => 'Cuticle Care Oil', 'category' => 'Nails', 'subcategory' => 'Nail Care', 'price' => 349],
            ['product_name' => 'Gel Nail Polish - Classic Red', 'category' => 'Nails', 'subcategory' => 'Nail Polish', 'price' => 499],
            ['product_name' => 'Beard Care Oil', 'category' => 'Grooming', 'subcategory' => 'Beard Care', 'price' => 449],
            ['product_name' => 'Beard Softening Balm', 'category' => 'Grooming', 'subcategory' => 'Beard Care', 'price' => 549],
            ['product_name' => 'Spa Aroma Massage Oil', 'category' => 'Spa', 'subcategory' => 'Massage Oil', 'price' => 799],
            ['product_name' => 'Disposable Facial Headband Pack', 'category' => 'Salon Supplies', 'subcategory' => 'Consumables', 'price' => 299],
            ['product_name' => 'Professional Hair Styling Wax', 'category' => 'Hair Care', 'subcategory' => 'Styling', 'price' => 699],
        ];

        $productModels = collect($productData)->map(function (array $row, int $index) use ($today) {
            return Product::create([
                ...$row,
                'status' => $index === 19 ? 'inactive' : 'active',
                'created_at' => $today->copy()->subDays(70 - min($index * 2, 65)),
                'updated_at' => $today->copy()->subDays(70 - min($index * 2, 65)),
            ]);
        })->values();

        // -----------------------------------------------------------------
        // 5. PAYMENT TYPES
        // -----------------------------------------------------------------
        $paymentTypes = PaymentType::where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($paymentTypes->isEmpty()) {
            foreach (['Cash', 'UPI', 'Card', 'EC'] as $name) {
                PaymentType::create(['name' => $name, 'is_active' => true]);
            }

            $paymentTypes = PaymentType::where('is_active', true)
                ->orderBy('id')
                ->get();
        }

        // -----------------------------------------------------------------
        // 6. PRODUCT PURCHASE / STOCK HISTORY
        // -----------------------------------------------------------------
        $purchaseNumber = 1;
        foreach ($productModels as $index => $product) {
            $purchaseBatches = ($index % 4 === 0) ? 2 : 1;

            for ($batch = 0; $batch < $purchaseBatches; $batch++) {
                $quantity = 8 + (($index * 3 + $batch * 5) % 25);
                $unitPrice = (float) $product->price;
                $total = round($quantity * $unitPrice, 2);
                $purchase = ProductPurchase::create([
                    'purchase_number' => 'PUR-' . str_pad((string) $purchaseNumber++, 3, '0', STR_PAD_LEFT),
                    'customer_id' => $customerModels[$index % $customerModels->count()]->id,
                    'purchase_date' => $today->copy()
                        ->subDays(7 + ($index * 2) + ($batch * 18))
                        ->format('Y-m-d'),
                    'payment_type_id' => $paymentTypes[($index + $batch) % $paymentTypes->count()]->id,
                    'total_amount' => $total,
                ]);

                ProductPurchaseItem::create([
                    'product_purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $total,
                ]);
            }
        }

        // -----------------------------------------------------------------
        // 7. JOB CARDS / SALES HISTORY
        // -----------------------------------------------------------------
        $statusPattern = [
            'completed', 'completed', 'completed', 'completed',
            'completed', 'completed', 'in_progress', 'pending',
            'completed', 'completed', 'cancelled', 'completed',
        ];

        $jobCardCount = 75;

        for ($index = 0; $index < $jobCardCount; $index++) {
            $customer = $customerModels[$index % $customerModels->count()];
            $primaryStaff = $activeStaff[$index % $activeStaff->count()];
            $service1 = $serviceModels[$index % $serviceModels->count()];
            $status = $statusPattern[$index % count($statusPattern)];

            // Keep a few records on today and most records distributed over
            // the previous 60 days so dashboard filters have useful data.
            $daysAgo = $index < 8
                ? 0
                : 1 + (($index * 7) % 59);

            $createdAt = $today->copy()
                ->subDays($daysAgo)
                ->setTime(9 + ($index % 10), ($index * 11) % 60);

            $discount = match ($index % 7) {
                0 => 300.00,
                1 => 150.00,
                2 => 500.00,
                default => 0.00,
            };

            $jobCard = JobCard::create([
                'job_card_name' => 'JC-' . $createdAt->format('ymd') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'service_id' => $service1->id,
                'staff_id' => $primaryStaff->id,
                'subcategory' => $service1->subcategory,
                'status' => $status,
                'discount_amount' => $discount,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addMinutes(35 + ($index % 45)),
            ]);

            $jobCard->customers()->sync([$customer->id]);
            $jobCard->staff()->sync([$primaryStaff->id]);

            $paymentType = $paymentTypes[$index % $paymentTypes->count()];

            $item1 = JobCardService::create([
                'job_card_id' => $jobCard->id,
                'service_id' => $service1->id,
                'subcategory' => $service1->subcategory,
                'amount' => $serviceAmounts[$service1->id],
                'payment_type_id' => $paymentType->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $item1->staff()->sync([$primaryStaff->id]);

            // Roughly 40% of appointments contain a second service.
            if ($index % 5 !== 1 && $index % 5 !== 4) {
                $service2 = $serviceModels[($index + 5) % $serviceModels->count()];
                $secondaryStaff = $activeStaff[($index + 2) % $activeStaff->count()];
                $paymentType2 = $paymentTypes[($index + 1) % $paymentTypes->count()];

                $item2 = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $service2->id,
                    'subcategory' => $service2->subcategory,
                    'amount' => $serviceAmounts[$service2->id],
                    'payment_type_id' => $paymentType2->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $item2->staff()->sync([$secondaryStaff->id]);
                $jobCard->staff()->syncWithoutDetaching([$secondaryStaff->id]);
            }

            // A few premium appointments include a third service.
            if ($index % 11 === 0) {
                $service3 = $serviceModels[($index + 9) % $serviceModels->count()];

                $item3 = JobCardService::create([
                    'job_card_id' => $jobCard->id,
                    'service_id' => $service3->id,
                    'subcategory' => $service3->subcategory,
                    'amount' => $serviceAmounts[$service3->id],
                    'payment_type_id' => $paymentTypes[($index + 2) % $paymentTypes->count()]->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $item3->staff()->sync([$primaryStaff->id]);
            }
        }

        // -----------------------------------------------------------------
        // 8. STAFF ATTENDANCE
        // -----------------------------------------------------------------
        $currentYear = (int) $today->format('Y');
        $currentMonth = (int) $today->format('m');

        $previousMonthDate = $today->copy()->subMonthNoOverflow();
        $previousYear = (int) $previousMonthDate->format('Y');
        $previousMonth = (int) $previousMonthDate->format('m');

        foreach ($activeStaff as $index => $member) {
            StaffAttendance::create([
                'staff_id' => $member->id,
                'year' => $previousYear,
                'month' => $previousMonth,
                'total_working_days' => 23,
                'present_days' => 20 + ($index % 4),
                'absent_days' => 3 - ($index % 2),
            ]);

            StaffAttendance::create([
                'staff_id' => $member->id,
                'year' => $currentYear,
                'month' => $currentMonth,
                'total_working_days' => 21,
                'present_days' => 17 + ($index % 4),
                'absent_days' => $index % 5 === 0 ? 2 : 1,
            ]);
        }

        // -----------------------------------------------------------------
        // 9. COMPLAINTS
        // -----------------------------------------------------------------
        $complaintTypes = ComplaintType::orderBy('id')->get();

        $complaints = [
            [
                'type' => 0,
                'subject' => 'Client waiting time exceeded during evening rush',
                'description' => 'Two clients waited more than 25 minutes before their scheduled service. Front desk requested additional support during the 5 PM to 7 PM peak period.',
                'days' => 2,
                'status' => 'Pending',
            ],
            [
                'type' => 1,
                'subject' => 'Late arrival for opening shift',
                'description' => 'Staff member reported 35 minutes after the scheduled opening time on a busy Saturday. Shift coverage was arranged by the floor supervisor.',
                'days' => 5,
                'status' => 'Resolved',
            ],
            [
                'type' => 2,
                'subject' => 'Hair dryer at styling station requires replacement',
                'description' => 'The dryer at Station 4 is overheating and should be replaced before the next maintenance cycle.',
                'days' => 8,
                'status' => 'In Progress',
            ],
            [
                'type' => 3,
                'subject' => 'Incorrect service handover between stylists',
                'description' => 'A colour-treatment appointment was handed over without recording the selected shade formula. The client record was corrected after verification.',
                'days' => 12,
                'status' => 'Resolved',
            ],
            [
                'type' => 4,
                'subject' => 'Retail stock count mismatch',
                'description' => 'Physical count showed a shortage of two professional shampoo units compared with the inventory register. Stock reconciliation is pending.',
                'days' => 15,
                'status' => 'Pending',
            ],
            [
                'type' => 5,
                'subject' => 'Client requested follow-up on service quality',
                'description' => 'Customer requested a follow-up call after a keratin treatment. The manager has assigned the case for review.',
                'days' => 18,
                'status' => 'In Progress',
            ],
        ];

        foreach ($complaints as $index => $complaint) {
            if (!isset($complaintTypes[$complaint['type']])) {
                continue;
            }

            Complaint::create([
                'complainant_staff_id' => $activeStaff[$index % $activeStaff->count()]->id,
                'complaint_type_id' => $complaintTypes[$complaint['type']]->id,
                'subject' => $complaint['subject'],
                'description' => $complaint['description'],
                'date_of_complaint' => $today->copy()->subDays($complaint['days'])->format('Y-m-d'),
                'status' => $complaint['status'],
            ]);
        }

        // -----------------------------------------------------------------
        // 10. MARKETING ACTIVITIES
        // -----------------------------------------------------------------
        $marketingData = [
            ['days' => 1, 'type' => 'Instagram Campaign', 'location' => 'Instagram & Facebook', 'count' => 1850, 'staff' => 0, 'notes' => 'Weekend hair spa promotion with before-and-after creative.'],
            ['days' => 3, 'type' => 'WhatsApp Promotion', 'location' => 'Existing Customer List', 'count' => 420, 'staff' => 2, 'notes' => 'Sent personalised weekday facial offer to returning customers.'],
            ['days' => 5, 'type' => 'Pamphlet Distribution', 'location' => 'City Centre & Shopping Complex', 'count' => 600, 'staff' => 1, 'notes' => 'Distributed new-customer offer cards during evening footfall.'],
            ['days' => 8, 'type' => 'Google Business Promotion', 'location' => 'Google Business Profile', 'count' => 310, 'staff' => 4, 'notes' => 'Promoted seasonal bridal and party makeup services.'],
            ['days' => 12, 'type' => 'Referral Campaign', 'location' => 'Existing Customers', 'count' => 95, 'staff' => 6, 'notes' => 'Referral cards issued to repeat customers with a next-visit benefit.'],
            ['days' => 16, 'type' => 'Local Partnership', 'location' => 'Nearby Boutique & Wedding Studio', 'count' => 18, 'staff' => 3, 'notes' => 'Partnership leads collected for bridal packages.'],
            ['days' => 21, 'type' => 'Festival Campaign', 'location' => 'Instagram, WhatsApp & In-Salon', 'count' => 2400, 'staff' => 0, 'notes' => 'Seasonal grooming campaign focused on family appointments.'],
            ['days' => 27, 'type' => 'Customer Reactivation', 'location' => 'CRM Customer List', 'count' => 160, 'staff' => 8, 'notes' => 'Follow-up campaign for customers without a visit in the last 60 days.'],
            ['days' => 34, 'type' => 'Influencer Collaboration', 'location' => 'Instagram Reels', 'count' => 5200, 'staff' => 5, 'notes' => 'Local creator collaboration featuring bridal makeup transformation.'],
            ['days' => 42, 'type' => 'Flyer Campaign', 'location' => 'Residential Community', 'count' => 450, 'staff' => 7, 'notes' => 'New customer introductory offer distributed to nearby households.'],
        ];

        foreach ($marketingData as $row) {
            MarketingActivity::create([
                'activity_date' => $today->copy()->subDays($row['days'])->format('Y-m-d'),
                'marketing_type' => $row['type'],
                'location' => $row['location'],
                'count' => $row['count'],
                'staff_id' => $activeStaff[$row['staff'] % $activeStaff->count()]->id,
                'notes' => $row['notes'],
            ]);
        }
    }
}
