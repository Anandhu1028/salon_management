<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
        });

        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('job_card_number')->nullable()->unique()->after('id');
            $table->decimal('subtotal', 12, 2)->default(0)->after('customer_id');
            $table->decimal('total', 12, 2)->default(0)->after('subtotal');
            $table->string('payment_method')->nullable()->after('total');
            $table->date('job_card_date')->nullable()->after('payment_method');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('id')->constrained('staff')->nullOnDelete();
            $table->string('complaint_type_text')->nullable()->after('staff_id');
            $table->text('reason')->nullable()->after('complaint_type_text');
            $table->text('action_taken')->nullable()->after('reason');
            $table->decimal('compensation', 12, 2)->default(0)->after('action_taken');
            $table->date('complaint_date')->nullable()->after('compensation');
            $table->foreignId('complainant_staff_id')->nullable()->change();
            $table->foreignId('complaint_type_id')->nullable()->change();
            $table->string('subject')->nullable()->change();
            $table->longText('description')->nullable()->change();
            $table->date('date_of_complaint')->nullable()->change();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('expense_date');
            $table->string('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('product_purchases', function (Blueprint $table) {
            $table->string('purchase_number')->nullable()->unique()->after('id');
            $table->decimal('unit_price', 12, 2)->default(0)->after('quantity');
            $table->decimal('total_amount', 12, 2)->default(0)->after('unit_price');
            $table->string('payment_method')->nullable()->after('total_amount');
            $table->text('notes')->nullable()->after('payment_method');
        });

        foreach (['Staff Salary', 'OT Staff', 'Staff Incentive', 'KSEB', 'Other'] as $name) {
            DB::table('expense_categories')->updateOrInsert(['name' => $name], ['status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::table('product_purchases', function (Blueprint $table) { $table->dropColumn(['purchase_number', 'unit_price', 'total_amount', 'payment_method', 'notes']); });
        Schema::table('complaints', function (Blueprint $table) { $table->dropConstrainedForeignId('staff_id'); $table->dropColumn(['complaint_type_text', 'reason', 'action_taken', 'compensation', 'complaint_date']); });
        Schema::table('job_cards', function (Blueprint $table) { $table->dropColumn(['job_card_number', 'subtotal', 'total', 'payment_method', 'job_card_date']); });
    }
};
