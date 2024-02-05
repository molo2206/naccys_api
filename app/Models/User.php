<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = "t_users";

    protected $fillable = [
        'name',
        'post_name',
        'prename',
        'email',
        'pswd',
        'phone',
        'gender',
        'status',
        'profil',
        'dateBorn',
        'adress',
        'roleid',
        'typeid',
        'id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsTo(RoleModel::class, 'roleid', 'id');
    }

    public function type()
    {
        return $this->belongsTo(TypePersonneModel::class, 'typeid', 'id');
    }
    public function count()
    {
        return $this->hasMany(CompteUserModel::class, 'userid', 'id');
    }

    public function compte(){
        return $this->belongsToMany(User::class, 't_compte_user', 'userid', 'typecompte');
    }

    public function permissions()
    {
        return $this->belongsToMany(RessourceModel::class, 't_roles_has_permissions', 'userid', 'ressourceid')->withPivot(['create', 'read', 'update', 'delete', 'status'])->as('access')->where('deleted', 0);
    }

    public function generateAccountNumber()
    {
        $digit = substr(str_shuffle("0123456789"), 0, 5); //generer un code à 5 chiffres
        $last = $this->count()->orderBy('created_at', 'desc')->get()->take(1); //récupération du derniere compte parmi les compte de l'utilisateur
        if ($last->count() > 0) { // on verifie s'il y a déjà un compte dans la base des données
            $last_num = $last[0]->count_number; // récupération du dernier numéro de compte de l'utilisateur
            $code = str_pad(substr(str_replace(substr_replace($last_num, "", 10), "", $last_num), 0, -5) + 1, 1, 0, STR_PAD_LEFT);
            $number = 'GOM' . date('y') . str_replace(substr_replace(substr_replace($last_num, "", 10), "", 5), "", substr_replace($last_num, "", 10)) . $code . $digit;
        } 
        return $number;
    }
}
