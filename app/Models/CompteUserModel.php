<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CompteUserModel extends Model
{
    use HasFactory, HasFactory, Notifiable, HasUuids;

    protected $table = "t_compte_user";
    protected $fillable = [
        'count_number',
        'userid',
        'typecompte',
        'currency',
        'id'
    ];
    public $timestamps = false;

    public function membre()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }

    public function transaction()
    {
        return $this->hasMany(TransactionModel::class, 'compteid', 'id')->orderby('created_at', 'Desc');
    }

    public function validTransactions()
    {
        return $this->transaction()->where('status', 0);
    }
    public function credit()
    {
        return $this->validTransactions()
            ->sum('credit');
    }

    public function debit()
    {
        return $this->validTransactions()
            ->sum('debit');
    }

    public function balance()
    {
        return $this->credit() - $this->debit();
    }

    public function allowWithdraw($amount): bool
    {
        return $this->balance() >= $amount;
    }

    public static function generateAccountNumber()
    {
        $digit = substr(str_shuffle("0123456789"), 0, 5); //generer un code à 5 chiffres
        $last = static::orderBy('created_at', 'desc')->get()->take(1); //récupération du derniere compte
        if ($last->count() > 0) { // on verifie s'il y a déjà un compte dans la base des données
            $last_num = $last[0]->count_number; // récupération du dernier numéro de compte
            $code = str_pad(substr(str_replace(substr_replace($last_num, "", 5), "", $last_num), 0, -6) + 1, 5, 0, STR_PAD_LEFT); // récupération du code à 5 chiffre et incrémentation, ex 00001 en 00002
            $number = 'GOM' . date('y') . $code . "0" . $digit; // formatage du nouveau numéro de compte
        } else {
            $number = "GOM" . date("y") . "00001" . "0" . $digit; // s'il y aucun compte on génère le premier compte        
        }

        return $number;
    }
}
