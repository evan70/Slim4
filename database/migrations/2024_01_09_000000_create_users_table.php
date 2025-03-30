<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Vytvorenie users tabuľky
Capsule::schema()->create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});