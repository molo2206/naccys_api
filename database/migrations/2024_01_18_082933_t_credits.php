<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_credits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('compteid')->constrained('t_compte_user');
            $table->foreignUuid('user1id')->constrained('t_users')->nullable();
            $table->foreignUuid('user2id')->constrained('t_users')->nullable();
            $table->text('designation')->nullable();
            $table->string('currency')->nullable();
            $table->double('debit')->nullable();
            $table->double('credit')->nullable();
            $table->double('solde')->nullable();
            $table->text('mount_lettre')->nullable();
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_credits');
    }

};
