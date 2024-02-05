<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_roles_has_permissions', function (Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('ressourceid')->constrained('t_resssources')->nullable();
            $table->foreignUuid('roleid')->constrained('t_roles')->nullable();
            $table->foreignUuid('userid')->constrained('t_users')->nullable();
            $table->boolean('create');
            $table->boolean('update');
            $table->boolean('delete');
            $table->boolean('read');
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_roles_has_permissions');
    }
};
