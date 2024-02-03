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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->unsignedBigInteger('customer_id');
            $table->string('courier');
            $table->string('courier_service');
            $table->string('courier_cost');
            $table->integer('weight');
            $table->integer('name');
            $table->string('phone');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('province_id');
            $table->text('address');
            $table->enum('status', array('pending', 'success', 'expired', 'failed'));
            $table->bigInteger('grand_total');
            $table->string('snap_token')->nullable();
            $table->timestamps();

            // Relationship Customer
            $table->foreign('customer_id')->references('id')->on('customers');

            // Relationship City
            $table->foreign('city_id')->references('id')->on('cities');

            // Relationship Province
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
