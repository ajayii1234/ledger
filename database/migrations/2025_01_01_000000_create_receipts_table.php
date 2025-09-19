<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
public function up()
{
Schema::create('receipts', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->date('date');
$table->string('vendor');
$table->string('category')->nullable();
$table->decimal('amount', 15, 6);
$table->string('currency', 10)->default('NGN');
$table->text('notes')->nullable();
$table->timestamps();
});
}


public function down()
{
Schema::dropIfExists('receipts');
}
};