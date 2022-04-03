<?php

use App\Enums\Status;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable();
            $table->string('provider_reference')->nullable();
            $table->foreignUuid('user_id')->constrained();
            $table->string('transaction_type');
            $table->char('status')->default(Status::PROCESSING->value);
            $table->char('currency', 10)->default('NGN');
            $table->decimal('amount', 18, 2)->comment('transaction amount');
            $table->decimal('charge', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4);
            $table->string('mode')->nullable()->comment('mode of transaction');
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
        Schema::dropIfExists('transactions');
    }
};
