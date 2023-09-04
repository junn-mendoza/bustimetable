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
        Schema::create('journey', function (Blueprint $table) {
            $table->id();
            $table->string('from_StopPointRef');
            $table->integer('from_seqno');
            $table->string('from_DynamicDestinationDisplay');
            $table->string('from_activity');
            $table->string('from_timingstatus');
            $table->string('to_StopPointRef');
            $table->integer('to_seqno');
            $table->string('to_DynamicDestinationDisplay');
            $table->string('to_activity');
            $table->string('to_timingstatus');
            $table->string('runtime');
            $table->string('xml_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey');
    }
};
