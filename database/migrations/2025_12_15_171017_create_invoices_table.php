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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('code')->unique();
            $table->integer('amount');
            $table->string('status');

            $table->date('issue_date');
            $table->date('due_date');
            $table->date('payment_date')->nullable();

            $table->foreignId('customer_id');


            $table->json('metadata')->nullable();

            // team
            $table->foreignId('team_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
