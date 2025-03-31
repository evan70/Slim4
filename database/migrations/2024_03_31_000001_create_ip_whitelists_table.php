<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return new class extends Migration
{
    public function up()
    {
        Capsule::schema()->create('ip_whitelists', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique('ip_address');
        });
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('ip_whitelists');
    }
};