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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("account_id")->constrained("accounts");
            $table->double("amount");
            $table->date("date_paid")->default(date('Y-m-d'));
            $table->string("transaction_type")->default("credit");
            $table->string("purpose")->default("daily_contribution");
            $table->string("status")->default("completed");
            $table->foreignId("created_by")->constrained("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
