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
        Schema::create('routelink', function (Blueprint $table) {
            $table->id();
            $table->string('routeid');
            $table->decimal('long', 8, 5);
            $table->decimal('lang', 8, 5);
            $table->string('xml_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routelink');
    }
};
