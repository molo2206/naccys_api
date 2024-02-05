<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::create('t_type_personne', function (Blueprint $table) {

                $table->uuid('id')->primary();
                $table->string('name')->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('deleted')->default(true);
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('t_type_personne');
        }
};
