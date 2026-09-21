<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('en_preparation'); // en_preparation, expediee, livree, annulee
            $table->date('shipped_at')->nullable();
            $table->date('estimated_delivery_at')->nullable();
            $table->string('delivery_method')->nullable(); // colissimo, chronopost, relais, etc.
            $table->string('tracking_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
