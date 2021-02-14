<?php

use App\Enums\BookingDuration;
use App\Enums\BookingStatus;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Expert::class)->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()
                ->onDelete('cascade');
            $table->tinyInteger('status')->default(BookingStatus::ACCEPTED);
            $table->tinyInteger('duration')->default(BookingDuration::QUARTER);
            $table->timestamp('start_at');
            $table->timestamp('finish_at');
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
}
