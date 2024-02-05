<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CreditModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_credits";
     protected $fillable = [
        'designation',
        'currency',
        'debit',
        'credit',
        'solde',
        'method',
        'memberid',
        'mount_lettre',
        'user1id',
        'user2id',
        'date',
        'id',
        'description'
    ];

    public function agent1(){
        return $this->belongsTo(User::class,'user1id' ,'id')->where('status',1);
    }

    public function agent2(){
        return $this->belongsTo(User::class,'user2id' ,'id')->where('status',1);
    }
}
