<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plugin_version_zencart_version', function (Blueprint $table) {
            $table->foreignId('plugin_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zencart_version_id')->constrained()->cascadeOnDelete();
            $table->primary(['plugin_version_id', 'zencart_version_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_version_zencart_version');
    }
};
