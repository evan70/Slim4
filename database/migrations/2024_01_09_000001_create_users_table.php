<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->boolean('is_admin')->default(false);
    $table->string('remember_token')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamps();
});
