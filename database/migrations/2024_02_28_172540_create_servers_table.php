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

        /*Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model', 50);
            $table->string('ram', 20);
            $table->string('hdd', 20);
            $table->string('location', 30);
            $table->decimal('price');

            $table->foreign('curated_by')->references('id')->on('accounts');
            $table->foreign('deleted_by')->references('id')->on('accounts');


        });

        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model', 50);
            $table->string('ram', 20);
            $table->string('hdd', 20);
            $table->string('location', 30);
            $table->decimal('price');

            $table->foreign('curated_by')->references('id')->on('accounts');
            $table->foreign('deleted_by')->references('id')->on('accounts');


        });

        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model', 50);
            $table->string('ram', 20);
            $table->string('hdd', 20);
            $table->string('location', 30);
            $table->decimal('price');

            $table->foreign('curated_by')->references('id')->on('accounts');
            $table->foreign('deleted_by')->references('id')->on('accounts');


        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
