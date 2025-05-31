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
        Schema::create('plugin_versions', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('version');
            $table->string('vc_url')->nullable();
            $table->integer('count')->default(0);
            $table->enum('status', ['closed', 'locked', 'hidden', 'open'])->default('open');
            $table->boolean('is_encapsulated')->default(false);
            $table->foreignId(('user_id'))->constrained();
            $table->foreignId(('plugin_id'))->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_versions');
    }
};
