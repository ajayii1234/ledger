<?php


// database/migrations/2025_09_24_000000_create_ocr_results_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcrResultsTable extends Migration
{
    public function up()
    {
        Schema::create('ocr_results', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename')->nullable();
            $table->string('path')->nullable();
            $table->longText('raw_text')->nullable();
            $table->json('parsed')->nullable(); // parsed key/value array
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ocr_results');
    }
}
