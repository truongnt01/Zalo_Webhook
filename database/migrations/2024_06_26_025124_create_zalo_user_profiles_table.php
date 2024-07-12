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
        Schema::create('zalo_user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('state')->default('hasValue');
            $table->string('name')->nullable();
            $table->string('avatar')->nullable();
            $table->string('idByOA')->nullable();
            $table->boolean('followedOA')->default(false);
            $table->boolean('isSensitive')->default(false);
            $table->string('mobile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zalo_user_profiles');
    }
};
