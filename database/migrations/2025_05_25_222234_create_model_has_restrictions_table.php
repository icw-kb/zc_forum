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
        Schema::create('model_has_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('restriction');
            $table->enum('restriction_gate_method', ['list', 'create', 'read', 'update', 'delete']);
            $table->json('restriction_values')->nullable();
            $table->morphs('restrictable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_restrictions');
    }
};
