<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
// database/migrations/xxxx_xx_xx_create_bookings_table.php

public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Usuario que hace la reserva (users_app)
            $table->foreignId('user_app_id')->constrained('users_app')->onDelete('cascade');

            // Servicio reservado
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            $table->float('price');
            $table->float('duration');
            $table->string('address');
            $table->timestamp('service_day');
            $table->string('status')->default('pending'); // pending, paid, cancelled, etc
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
