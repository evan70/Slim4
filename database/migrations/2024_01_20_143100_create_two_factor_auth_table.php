<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwoFactorAuthTable extends Migration
{
    public function up()
    {
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('secret_key');
            $table->boolean('is_enabled')->default(false);
            $table->string('recovery_codes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('two_factor_auth');
    }
}