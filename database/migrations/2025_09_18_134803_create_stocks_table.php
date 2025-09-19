<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // each user has their own tracked stocks
            $table->string('symbol')->index();
            $table->string('name')->nullable();
            $table->tinyInteger('decimals_default')->default(2);
            $table->timestamps();
            $table->unique(['user_id','symbol']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};
