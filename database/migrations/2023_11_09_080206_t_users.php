<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */

     public function up()
     {
     Schema::create('t_users', function (Blueprint $table) {

             $table->uuid('id')->primary();
             $table->string('name')->nullable();
             $table->string('post_name')->nullable();
             $table->string('prename')->nullable();
             $table->string('phone')->nullable();
             $table->string('email')->nullable();
             $table->string('pswd')->nullable();
             $table->text('profil')->nullable();
             $table->string('adress')->nullable();
             $table->boolean('status')->default(true);
             $table->boolean('deleted')->default(true);
             $table->enum('gender',['masculin','feminin'])->nullable();
             $table->date('dateBorn');
             $table->timestamps();
         });
     }

     public function down()
     {
         Schema::dropIfExists('t_users');
     }
};
