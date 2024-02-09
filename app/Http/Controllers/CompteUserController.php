<?php

namespace App\Http\Controllers;

use App\Mail\Createcount;
use App\Mail\Createmembre;
use App\Models\CompteUserModel;
use App\Models\IncrementeModel;
use App\Models\TypeCompteModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CompteUserController extends Controller
{

    public function create_count(Request $request)
    {
        $request->validate([
            'userid' => 'required',
            'typecompte' => 'required',
            'currency' => 'required',
        ]);
        $user = User::find($request->userid);
        if ($user) {
            if (count($user->count) >= 10) {
                return json_encode(['message' => "Vous avez déjà atteint les nombres total des comptes!"], 402);
            }
            if ($request->typecompte == 'courant' || $request->typecompte == "credit") {
                $compte = CompteUserModel::where('typecompte', 'credit')
                    ->where('userid', $request->userid)
                    ->get();
                if (count($compte) > 0) {
                    return response()->json([
                        "message" => 'Désolé on ne peut pas avoir deux comptes credits!',
                        "code" => 402,
                    ], 402);
                } else {
                    $user->count()->create([
                        'count_number' => $user->generateAccountNumber(),
                        'userid' => $user->id,
                        'currency' => $request->currency,
                        'typecompte' => $request->typecompte,
                        'id' => count(CompteUserModel::all()) + 1
                    ]);

                    // Mail::to($user->email)->send(new Createmembre(
                    //     $user->name,
                    //     $user->post_name,
                    //     $request->typecompte,
                    //     $request->currency,
                    //     $user->prename,
                    //     $user->generateAccountNumber(),
                    // ));
                    return response()->json([
                        "message" => "ok",
                        "code" => 200,
                        "data" => User::with('roles', 'type', 'permissions', 'count')->where('id', $request->userid)->first(),
                    ], 200);
                }
            } else {
                return response()->json([
                    "message" => 'Type compte doit courant ou credit!',
                    "code" => 402,
                ], 402);
            }
        } else {
            return response()->json([
                "message" => 'Ce membre n\'est pas recconue dans le système!',
                "code" => 402,
            ], 402);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'typecompte' => 'required',
            'currency' => 'required',
        ]);

        $account = CompteUserModel::where('deleted', 0)->find($id);
        if ($account) {
            if (count($account->transaction) > 0) {
                return response()->json([
                    "message" => "Modification impossible, ce compte a déjà des transactions"
                ], 402);
            }

            $account->typecompte = $request->typecompte;
            $account->currency = $request->currency;
            $account->save();
            return response()->json([
                "message" => "Modification reussi avec succès"
            ], 200);
        } else {
            return response()->json([
                "message" => "Id introuvable"
            ], 404);
        }
    }
    public function listtypecompte()
    {
        return response()->json([
            "message" => 'success',
            "code" => 200,
            "data" => TypeCompteModel::all(),
        ], 200);
    }

    public function recherche(Request $request)
    {
        $data = CompteUserModel::with('membre', 'transaction.agent');
        if ($request->keyword) {
            $data->where('count_number', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_users.name', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_users.post_name', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_users.prename', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_users.email', 'like', '%' . $request->keyword . '%')
                ->orwhere('t_users.phone', 'like', '%' . $request->keyword . '%')
                ->leftJoin('t_users', 't_users.id', '=', 't_compte_user.userid')
                ->select(
                    't_compte_user.id',
                    't_compte_user.count_number',
                    't_users.name',
                    't_users.post_name',
                    't_users.prename',
                    't_users.profil',
                );
        }

        $alldata = $data->get();
        return response([
            "message" => "Success",
            "code" => 200,
            "data" => $alldata,
        ], 200);
    }

    public function locked_count($id)
    {
        if (CompteUserModel::where('id', $id)->where('status', 0)->exists()) {
            $compte = CompteUserModel::where('id', $id)->first();
            $compte->status = 1;
            $compte->update();
            return response()->json([
                "message" => 'success',
                "code" => 200,
                "data" => CompteUserModel::where('status', 0)->get(),
            ], 200);
        } else {
            return response()->json([
                "message" => 'Ce compte n\'existe pas!',
                "code" => 402,
            ], 402);
        }
    }
    public function getmemberbycount_number($count_number)
    {
        $count = CompteUserModel::where('count_number', $count_number)->first();
        $countt = CompteUserModel::with('membre', 'transaction.agent')->where('count_number', $count_number)->first();
        if ($countt == null) {
            return response()->json([
                "message" => 'Numèro de compte introuvable!',
                "code" => 402,
            ], 402);
        } else {
            $count = CompteUserModel::find($countt->id);
            return response()->json([
                "message" => 'Count exists',
                "code" => 200,
                "balance" => $count->balance(),
                "data" => $countt,
            ], 200);
        }
    }

    // public function create(Request $request)
    // {
    //     $request->validate(['name' => 'required']);
    //     $user = User::create([
    //         "name" => $request->name
    //     ]);
    //     $user->comptes()->create(['num' => Compte::generateAccountNumber()]);
    //     return response()->json([
    //         "message" => 'ok'
    //     ], 200);
    // }
    // public function createAccount(Request $request)
    // {
    //     $request->validate(['id' => 'required']);
    //     $user = User::find($request->id);
    //     if ($user) {
    //         if (count($user->comptes) >= 10) {
    //             return json_encode(['message' => "Compte total 10"], 402);
    //         }
    //         $user->comptes()->create(['num' => $user->generateAccountNumber()]);
    //         return response()->json([
    //             "message" => "ok"
    //         ], 200);
    //     } else {
    //         return json_encode(["message" => "not found"], 404);
    //     }
    // }

    public function destroy($id)
    {
        $account = CompteUserModel::where('deleted', 0)->find($id);
        if ($account) {
            if (count($account->transaction) > 0) {
                return response()->json([
                    "message" => "Suppression impossible, ce compte a déjà des transactions"
                ], 402);
            }

            $account->deleted = 1;
            $account->save();
            return response()->json([
                "message" => "Suppression reussi avec succès"
            ], 200);
        } else {
            return response()->json([
                "message" => "Id introuvable"
            ], 404);
        }
    }
}
