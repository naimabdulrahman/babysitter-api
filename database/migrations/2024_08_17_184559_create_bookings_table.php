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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->string('phone_number');
            $table->text('address');
            $table->dateTime('reservation_datetime');
            $table->json('kid_detail');
            $table->timestamps();

            // Add unique constraint
            $table->unique(['fullname', 'email', 'phone_number', 'address', 'reservation_datetime', 'kid_detail'], 'booking_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
