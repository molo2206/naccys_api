<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TypeCompteModel extends Model
{
    use HasFactory,HasFactory,Notifiable,HasUuids;

    protected $table="t_type_compte";
    protected $fillable = [
        'designation',
        'id'
    ];


}
