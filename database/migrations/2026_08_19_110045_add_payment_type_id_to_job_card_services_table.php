<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment type to each job card service item.
     */
   public function up(): void
{
    if (!Schema::hasColumn('job_card_services', 'payment_type_id')) {

        // 1. Add the column temporarily as nullable
        Schema::table('job_card_services', function (Blueprint $table) {
            $table->foreignId('payment_type_id')
                ->nullable()
                ->after('amount')
                ->constrained('payment_types')
                ->restrictOnDelete();
        });
    }

    /*
     * 2. Backfill from the existing job card payment type.
     */
    if (Schema::hasColumn('job_cards', 'payment_type_id')) {

        DB::table('job_card_services as jcs')
            ->join(
                'job_cards as jc',
                'jc.id',
                '=',
                'jcs.job_card_id'
            )
            ->whereNull('jcs.payment_type_id')
            ->whereNotNull('jc.payment_type_id')
            ->update([
                'jcs.payment_type_id' => DB::raw(
                    'jc.payment_type_id'
                ),
            ]);
    }

    /*
     * 3. Handle any remaining old records.
     *
     * Existing records without a payment type cannot be made
     * NOT NULL unless we give them a valid payment type.
     *
     * Use Cash as the fallback for historical records only.
     */
    $cashPaymentTypeId = DB::table('payment_types')
        ->where('name', 'Cash')
        ->value('id');

    if (!$cashPaymentTypeId) {
        throw new \RuntimeException(
            'Cash payment type was not found in payment_types.'
        );
    }

    DB::table('job_card_services')
        ->whereNull('payment_type_id')
        ->update([
            'payment_type_id' => $cashPaymentTypeId,
        ]);

    /*
     * 4. Now that every existing record has a payment type,
     * make the column NOT NULL.
     */
    Schema::table('job_card_services', function (Blueprint $table) {
        $table->foreignId('payment_type_id')
            ->nullable(false)
            ->change();
    });
}

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_card_services', 'payment_type_id')) {

            Schema::table('job_card_services', function (Blueprint $table) {
                $table->dropForeign([
                    'payment_type_id',
                ]);

                $table->dropColumn('payment_type_id');
            });
        }
    }
};