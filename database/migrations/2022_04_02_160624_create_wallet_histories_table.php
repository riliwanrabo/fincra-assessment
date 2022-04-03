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
    public function up()
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained();
            $table->foreignUuid('transaction_id')->nullable();
            $table->decimal('amount', 18, 4);
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->decimal('previous_balance', 18, 4);
            $table->decimal('current_balance', 18, 4);
            $table->string('description')->nullable();
            $table->string('causer')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('wallet_histories');
    }
};
