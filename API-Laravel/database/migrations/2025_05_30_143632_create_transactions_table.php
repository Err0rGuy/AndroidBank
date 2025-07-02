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
            $table->foreignId('user_id')->constrained('users');
            $table->string('destination_account')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['transfer', 'withdraw', 'inquiry']);
            $table->text('description')->nullable();
            $table->enum('status', ['success', 'fail']);
            $table->string('reference_number')->unique();
            $table->timestamp('created_at')->useCurrent();
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
