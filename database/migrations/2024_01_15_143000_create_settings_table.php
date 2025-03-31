<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$schema = Capsule::schema();

$schema->create('settings', function ($table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();
});