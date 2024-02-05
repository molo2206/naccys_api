<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_code_validations', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->integer('code')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('t_code_validations');
    }
};
