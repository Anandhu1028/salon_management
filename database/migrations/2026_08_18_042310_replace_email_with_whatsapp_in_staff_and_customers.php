<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->string('mobile_country_code', 5)->nullable()->after('name');
            $table->string('whatsapp_country_code', 5)->nullable()->after('mobile_number');
            $table->string('whatsapp_number', 20)->nullable()->after('whatsapp_country_code');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->string('mobile_country_code', 5)->nullable()->after('name');
            $table->string('whatsapp_country_code', 5)->nullable()->after('mobile_number');
            $table->string('whatsapp_number', 20)->nullable()->after('whatsapp_country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->dropColumn(['mobile_country_code', 'whatsapp_country_code', 'whatsapp_number']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->dropColumn(['mobile_country_code', 'whatsapp_country_code', 'whatsapp_number']);
        });
    }
};
