<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::create('t_incremente_count', function (Blueprint $table) {

                $table->uuid('id')->primary();
                $table->integer('incrementation')->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('deleted')->default(true);
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('t_incremente_count');
        }
};
