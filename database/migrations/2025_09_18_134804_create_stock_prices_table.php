<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->dateTime('date_time')->nullable();
            // store full precision; display will be rounded
            $table->decimal('price', 18, 8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_prices');
    }
};
