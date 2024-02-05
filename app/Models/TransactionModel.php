<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TransactionModel extends Model
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

     protected $table="t_user_transaction";

     protected $fillable = [
        'designation',
        'currency',
        'debit',
        'credit',
        'solde',
        'method',
        'compteid',
        'mount_lettre',
        'userid',
        'date',
        'id',
        'description'
    ];
    public function agent(){
        return $this->belongsTo(User::class,'userid' ,'id')->where('status',1);
    }

    public function compte()
    {
        return $this->belongsTo(CompteUserModel::class, 'compteid', 'id');
    }

}
