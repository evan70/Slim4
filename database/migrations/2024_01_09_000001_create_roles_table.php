<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// Vytvorenie roles tabuľky
Capsule::schema()->create('roles', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});

// Vytvorenie pivot tabuľky pre user_roles
Capsule::schema()->create('user_roles', function ($table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});