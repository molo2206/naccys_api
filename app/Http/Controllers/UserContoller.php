<?php

namespace App\Http\Controllers;

use App\Mail\Createcount;
use App\Mail\Createmembre;
use App\Mail\NewPswd;
use App\Mail\Verificationmail;
use App\Models\codeValidation;
use App\Models\CompteUserModel;
use App\Models\IncrementeModel;
use App\Models\TypePersonneModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserContoller extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'pswd' => 'required',
        ]);

        if (User::where('email', $request->email)->exists()) {
            $user = User::where('email', $request->email)->where('deleted', 0)->first();

            if ($user->status == 1) {
                if (Hash::check($request->pswd, $user->pswd)) {
                    $token = $user->createToken("accessToken")->plainTextToken;
                    return response()->json([
                        "message" => 'success',
                        "data" => User::with('roles', 'type', 'permissions')->find($user->id),
                        "status" => 1,
                        "token" => $token,
                    ], 200);
                } else {
                    return response()->json([
                        "message" => 'Le mot de passe est incorrect',
                        "code" => 422,
                    ], 422);
                }
            } else {
                return response()->json([
                    "message" => 'Votre compte n\'est pas activé',
                    "code" => 422,
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "Cette adresse email n'existe pas",
            ], 404);
        }
    }

    public function Lost_pswd(Request $request)
    {
        $request->validate([
            "email" => 'required|email',
            "pswd" => [
                'required',
                'min:8',
            ],
            "pswdconfirm" => [
                'required',
                'min:8',
            ],
        ]);
        if (User::where('email', $request->email)->exists() == false) {
            return response()->json([
                "message" => 'Cette adresse n\'existe pas',
            ], 402);
        } else {
            if ($request->pswd != $request->pswdconfirm) {
                return response()->json([
                    "message" => 'Mot de passe n\'est pas identique',
                ], 402);
            } else {
                $change = User::where('email', $request->email)->first();
                $change->update([
                    "pswd" => Hash::make($request->pswd),
                ]);
                Mail::to($request->email)->send(new NewPswd($request->email, $request->pswd));
                return response()->json([
                    "message" => "Votre mot de passe à été modifier avec succès.",
                    "code" => 200,
                ], 200);
            }
        }
    }
    public function changePswdProfil(Request $request)
    {

        $request->validate([
            "old_pswd" => "required",
            "new_pswd" => "required",
        ]);

        $datauser = Auth::user();
        $user = User::where('id', $datauser->id)->first();
        if (Hash::check($request->old_pswd, $user->pswd)) {

            $user->pswd = Hash::make($request->new_pswd);
            $user->save();

            return response()->json([
                "message" => "Modification mot de passe réussie!",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Ancien mot de passe incorrect!",
                "code" => 422,
            ], 422);
        }
    }

    public function reinitialiser_pswd(Request $request)
    {

        $request->validate([
            "email" => "required",
            "pswd" => "required",
            "otp" => "required",
        ]);

        $user = User::where('email', $request->email)->first();
        $code = codeValidation::where('email', $user->email)->where('code', $request->otp)->where('status', 0)->first();
        if ($code) {

            $user->pswd = Hash::make($request->pswd);
            $user->save();

            $code->status = 1;
            $code->save();

            return response()->json([
                "message" => "Réinitialisation mot de passe réussie!",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Otp non valide!",
                "code" => 422,
            ], 422);
        }
    }

    public function editProfile(Request $request)
    {
        $user = Auth::user();

        if (!Auth::user()) {
            return response()->json([
                "message" => "Identifant incorrect",
            ], 422);
        } else {
            if ($user) {
                if ($request->name == null) {
                    $user->name = $user->name;
                } else {
                    $user->name = $request->name;
                }

                if ($request->post_name == null) {
                    $user->post_name = $user->post_name;
                } else {
                    $user->post_name = $request->post_name;
                }

                if ($request->prename == null) {
                    $user->prename = $user->prename;
                } else {
                    $user->prename = $request->prename;
                }

                if ($request->phone == null) {
                    $user->phone = $user->phone;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }
                if ($request->dateBorn == null) {
                    $user->dateBorn = $user->dateBorn;
                } else {
                    $user->dateBorn = $request->dateBorn;
                }
                if ($request->email == null) {
                    $user->email = $user->email;
                } else {
                    $user->email = $request->email;
                }

                if ($request->adress == null) {
                    $user->adress = $user->adress;
                } else {
                    $user->adress = $request->adress;
                }
                $user->save();
                return response()->json([
                    "message" => "Profile modifier avec succès",
                    "data" => User::with('roles', 'type', 'permissions')->where('id', $user->id)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant user incorrect",
                ], 422);
            }
        }
    }

    public function UpdateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if ($id == null) {
            return response()->json([
                "message" => "Identifant user incorrect",
            ], 422);
        } else {

            if ($user) {
                if ($request->name == null) {
                    $user->name = $user->name;
                } else {
                    $user->name = $request->name;
                }

                if ($request->post_name == null) {
                    $user->post_name = $user->post_name;
                } else {
                    $user->post_name = $request->post_name;
                }

                if ($request->prename == null) {
                    $user->prename = $user->prename;
                } else {
                    $user->prename = $request->prename;
                }

                if ($request->phone == null) {
                    $user->phone = $user->phone;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }
                if ($request->dateBorn == null) {
                    $user->dateBorn = $user->dateBorn;
                } else {
                    $user->dateBorn = $request->dateBorn;
                }
                if ($request->email == null) {
                    $user->email = $user->email;
                } else {
                    $user->email = $request->email;
                }

                if ($request->adress == null) {
                    $user->adress = $user->adress;
                } else {
                    $user->adress = $request->adress;
                }

                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }

                if ($request->roleid == null) {

                    $user->roleid = $user->roleid;
                } else {

                    $user->roleid = $request->roleid;
                }

                $image = ImageController::uploadImageUrl($request->profil, '/uploads/membre/');
                if ($request->profil == null) {
                    $user->profil = $user->profil;
                } else {
                    $user->profil = $image;
                }
                $user->save();

                return response()->json([
                    "message" => "Profile modifier avec succès",
                    "data" => $user::with('roles', 'type', 'permissions')->where('id', $user->id)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant incorrect",
                ], 422);
            }
        }
    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            "email" => "required",
            "otp" => "required",
        ]);

        $user = User::where('email', $request->email)->first();
        $code = codeValidation::where('email', $user->email)->where('code', $request->otp)->where('status', 0)->first();
        if ($code) {
            return response()->json([
                "message" => "success",
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Code de réinitialisation incorrect!",
                "code" => 422,
            ], 422);
        }
    }

    public function askcodevalidateion(Request $request)
    {
        $request->validate([
            "email" => "required",
        ]);
        if (User::where('email', $request->email)->exists()) {
            $code = mt_rand(1, 9999);
            $val = codeValidation::where('email', $request->email)->where('status', 0)->first();
            if ($val) {
                codeValidation::create(['email' => $request->email, 'code' => $code]);
                $val->code = $code;
                $val->status = 1;
                $val->save();
            } else {
                codeValidation::create(['email' => $request->email, 'code' => $code]);
            }
            Mail::to($request->email)->send(new Verificationmail($request->email, $code));
            return response()->json([
                "message" => "Un code de validation vous a été envoyé sur cette adresse " . $request->email,
                "code_validation" => $code,
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette adresse n'est pas reconnue dans le système!",
            ], 422);
        }
    }

    public function getusers()
    {
        return response()->json([
            "message" => "Listes des utilisateurs!",
            "data" => User::with('roles', 'type', 'permissions')->get(),
        ]);
    }

    public function create_agent(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'post_name' => 'required',
            'prename' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'adress' => 'required',
            'roleid' => 'required',
        ]);
        $agent = TypePersonneModel::where('name', 'Agent')->first();
        if (User::where('email', $request->email)
            ->orwhere('phone', $request->phone)->exists()
        ) {
            return response()->json([
                "message" => "Adresse e-mail ou Numèro téléphone existe déjà dans le système",
                "code" => 402,
            ], 402);
        } else {
            $pswd = 123456;
            if ($request->profil == null) {
                User::create([
                    'name' => $request->name,
                    'post_name' => $request->post_name,
                    'prename' => $request->prename,
                    'email' => $request->email,
                    'pswd' => Hash::make($pswd),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'profil' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                    'dateBorn' => $request->date,
                    'adress' => $request->address,
                    'roleid' => $request->roleid,
                    'typeid' => $agent->id,
                ]);
                Mail::to($request->email)->send(new Createcount($request->email, $pswd));
                return response([
                    "message" => "L'utilisateur" . $request->name
                        . " " . $request->post_name
                        . " " . $request->prename . "est créer avec succès!",
                    "code" => 200,
                    "default password" => $pswd,
                    "data" => User::with('roles', 'type', 'permissions')->where('email', $request->email)->first(),
                ]);
            } else {
                $photo = ImageController::uploadImageUrl($request->profil, '/uploads/agent/');
                User::create([
                    'name' => $request->name,
                    'post_name' => $request->post_name,
                    'prename' => $request->prename,
                    'email' => $request->email,
                    'pswd' => Hash::make($pswd),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'profil' => $photo,
                    'dateBorn' => $request->date,
                    'adress' => $request->address,
                    'roleid' => $request->roleid,
                    'typeid' => $agent->id,
                ]);
                return response([
                    "message" => "L'utilisateur" . $request->name
                        . " " . $request->post_name
                        . " " . $request->prename . "est créer avec succès!",
                    "code" => 200,
                    "default password" => $pswd,
                    "data" => User::with('roles', 'type', 'permissions')->where('email', $request->email)->first(),
                ], 200);
            }
        }
    }
    public function create_membre(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'post_name' => 'required',
            'prename' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'adress' => 'required',
        ]);

        $agent = TypePersonneModel::where('name', 'Membre')->first();
        if (User::where('email', $request->email)
            ->orwhere('phone', $request->phone)->exists()
        ) {
            return response()->json([
                "message" => "Adresse e-mail ou Numèro téléphone existe déjà dans le système",
                "code" => 402,
            ], 402);
        } else {
            $pswd = 123456;
            if ($request->profil == null) {
                $user = User::create([
                    'name' => $request->name,
                    'post_name' => $request->post_name,
                    'prename' => $request->prename,
                    'email' => $request->email,
                    'pswd' => Hash::make($pswd),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'profil' => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                    'dateBorn' => $request->date,
                    'adress' => $request->adress,
                    'typeid' => $agent->id,
                ]);

                $typecompte = "courant";
                $user->count()->create([
                    'count_number' => CompteUserModel::generateAccountNumber(),
                    'userid' => $user->id,
                    'currency' => '$',
                    'typecompte' => $typecompte,
                    'id' => count(CompteUserModel::all()) + 1
                ]);

                Mail::to($request->email)->send(new Createmembre(
                    $user->name,
                    $user->post_name,
                    $typecompte,
                    $request->currency,
                    $user->prename,
                    CompteUserModel::generateAccountNumber()
                ));

                return response()->json([
                    "message" => 'success',
                    "code" => 200,
                    "data" => User::with('roles', 'type', 'permissions', 'count')->where('id', $user->id)->first(),
                ], 200);
            } else {

                $photo = ImageController::uploadImageUrl($request->profil, '/uploads/membre/');
                $user = User::create([
                    'name' => $request->name,
                    'post_name' => $request->post_name,
                    'prename' => $request->prename,
                    'email' => $request->email,
                    'pswd' => Hash::make($pswd),
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'profil' => $photo,
                    'dateBorn' => $request->date,
                    'adress' => $request->adress,
                    'typeid' => $agent->id,
                ]);

                $typecompte = "courant";
                $user->count()->create([
                    'count_number' => CompteUserModel::generateAccountNumber(),
                    'userid' => $user->id,
                    'currency' => '$',
                    'typecompte' => $typecompte,

                ]);

                Mail::to($request->email)->send(new Createmembre(
                    $user->name,
                    $user->post_name,
                    $typecompte,
                    $request->currency,
                    $user->prename,
                    CompteUserModel::generateAccountNumber()
                ));

                return response()->json([
                    "message" => 'success',
                    "code" => 200,
                    "data" => User::with('roles', 'type', 'permissions', 'count')->where('id', $user->id)->first(),
                ], 200);
            }
        }
    }
    public function UpdateMembre(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if ($id == null) {
            return response()->json([
                "message" => "Id membre est null!",
            ], 422);
        } else {

            if ($user) {
                if ($request->name == null) {
                    $user->name = $user->name;
                } else {
                    $user->name = $request->name;
                }

                if ($request->post_name == null) {
                    $user->post_name = $user->post_name;
                } else {
                    $user->post_name = $request->post_name;
                }

                if ($request->prename == null) {
                    $user->prename = $user->prename;
                } else {
                    $user->prename = $request->prename;
                }

                if ($request->phone == null) {
                    $user->phone = $user->phone;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }
                if ($request->dateBorn == null) {
                    $user->dateBorn = $user->dateBorn;
                } else {
                    $user->dateBorn = $request->dateBorn;
                }
                if ($request->email == null) {
                    $user->email = $user->email;
                } else {
                    $user->email = $request->email;
                }

                if ($request->adress == null) {
                    $user->adress = $user->adress;
                } else {
                    $user->adress = $request->adress;
                }

                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }

                if ($request->roleid == null) {

                    $user->roleid = $user->roleid;
                } else {

                    $user->roleid = $request->roleid;
                }

                $image = ImageController::uploadImageUrl($request->profil, '/uploads/membre/');
                if ($request->profil == null) {
                    $user->profil = $user->profil;
                } else {
                    $user->profil = $image;
                }

                $user->save();
                return response()->json([
                    "message" => "Membre modifier avec succès",
                    "data" => $user::with('roles', 'type', 'permissions', 'count')
                        ->where('id', $user->id)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant incorrect",
                ], 422);
            }
        }
    }

    public function get_type_personne()
    {
        return response()->json([
            "message" => "Type des personnnes",
            "code" => 200,
            "data" => TypePersonneModel::all(),
        ]);
    }
    public function getuserId($id)
    {
        if (User::where('id', $id)->exists()) {
            $user = User::where('id', $id)->first();
            return response()->json([
                "message" => 'success',
                "data" => User::with('roles', 'type', 'permissions')->where('id', $user->id)->first(),
            ], 200);
        } else {
            return response()->json([
                "message" => "cet utilisateur n'existe pas!",
            ], 404);
        }
    }

    public function getCountmember($userid)
    {
        $user = User::where('id', $userid)->first();
        if ($user) {
            return response()->json([
                "message" => "Liste des comptes de " . $user->name . " " . $user->post_name . " " . $user->prename,
                "data" => User::with('roles', 'type', 'permissions', 'count')->where('id', $user->id)->first(),
            ], 200);
        } else {

            return response()->json([
                "message" => "userid n'est pas reconnue dans le système!",
                "code" => 402,
            ], 402);
        }
    }

    public function getuser()
    {
        $agent = TypePersonneModel::where('name', 'Agent')->first();
        return response()->json([
            "message" => 'success',
            "data" => User::with('roles', 'type', 'permissions')->where('typeid', $agent->id)->where('deleted', 0)->orderBy('created_at', 'asc')->get(),
        ], 200);
    }

    public function getMembre()
    {
        $agent = TypePersonneModel::where('name', 'Membre')->first();
        return response()->json([
            "message" => 'success',
            "data" => User::with('roles', 'type', 'permissions', 'count')->orderby('name', 'asc')->where('typeid', $agent->id)->where('deleted', 0)->orderBy('created_at', 'asc')->get(),
        ], 200);
    }

    public function editImage(Request $request)
    {
        $request->validate([
            "image" => "required|image",
        ]);

        $image = ImageController::uploadImageUrl($request->image, '/uploads/agent/');
        $user = Auth::user();
        $user->profil = $image;
        $user->save();
        return response()->json([
            "message" => 'Photo de profile mise à jour',
            "status" => 1,
            "data" => User::with('roles', 'type', 'permissions')->where('id', $user->id)->first(),
        ], 200);
    }
    public function destroy($id)
    {
        $user = User::where('deleted', 0)->find($id);
        if ($user) {
            if ($user->id == Auth::user()->id) {
                return response()->json([
                    "message" => "Vous ne pouvez pas supprimer un compte qui est actif"
                ], 402);
            }
            foreach ($user->count()->get() as $account) {
                if (count($account->transaction) > 0) {
                    return response()->json([
                        "message" => "Ce membre a déjà des transactions"
                    ], 402);
                }
            }
            $user->deleted = 1;
            $user->save();
            return response()->json([
                "message" => "Suppression effectuée avec succès"
            ], 200);
        } else {
            return response()->json([
                "message" => "Id introuvable"
            ], 404);
        }
    }
}
