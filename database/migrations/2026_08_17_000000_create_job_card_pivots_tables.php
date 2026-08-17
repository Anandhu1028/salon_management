<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_card_customer')) {
            Schema::create('job_card_customer', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['job_card_id', 'customer_id']);
            });
        }

        if (!Schema::hasTable('job_card_staff')) {
            Schema::create('job_card_staff', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
                $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['job_card_id', 'staff_id']);
            });
        }

        Schema::table('job_cards', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
        });

        // Migrate existing customer_id data
        $existingJobCards = DB::table('job_cards')->select('id', 'customer_id', 'staff_id', 'created_at', 'updated_at')->get();
        $now = now();

        $customerPivots = [];
        $staffPivots = [];

        foreach ($existingJobCards as $jc) {
            if (!empty($jc->customer_id)) {
                $customerPivots[] = [
                    'job_card_id' => $jc->id,
                    'customer_id' => $jc->customer_id,
                    'created_at' => $jc->created_at ?? $now,
                    'updated_at' => $jc->updated_at ?? $now,
                ];
            }

            if (!empty($jc->staff_id)) {
                $staffPivots[] = [
                    'job_card_id' => $jc->id,
                    'staff_id' => $jc->staff_id,
                    'created_at' => $jc->created_at ?? $now,
                    'updated_at' => $jc->updated_at ?? $now,
                ];
            }
        }

        if (!empty($customerPivots)) {
            foreach (array_chunk($customerPivots, 100) as $chunk) {
                DB::table('job_card_customer')->insertOrIgnore($chunk);
            }
        }

        if (!empty($staffPivots)) {
            foreach (array_chunk($staffPivots, 100) as $chunk) {
                DB::table('job_card_staff')->insertOrIgnore($chunk);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_card_staff');
        Schema::dropIfExists('job_card_customer');
    }
};
