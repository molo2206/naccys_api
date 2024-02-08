<?php

namespace App\Http\Controllers;

use App\Models\CompteUserModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function PHPSTORM_META\type;

class TransactionController extends Controller
{
    public function make_transaction(Request $request)
    {
        $request->validate([
            'type' => "required",
            'amount' => "required",
            'mount_lettre' => "required",
            'compteid' => "required",
            'password' => 'required',
            'date' => 'required',
            'description' => 'required',
        ]);


        $compte = CompteUserModel::with('membre', 'transaction.agent')->where('id', $request->compteid)->orderby('created_at', 'DESC')->first();
        $count = CompteUserModel::where('id', $compte->id)->first();
        $user = Auth::user();
        if ($count) {
            if ($user->status == 1) {
                if (Hash::check($request->password, $user->pswd)) {
                    if ($request->type == 'credit' || $request->type == 'debit') {
                        if (!$compte) {
                            return response()->json([
                                "message" => "Le numèro de compte n'est pas recconue dans le système!",
                                "code" => "402"
                            ], 402);
                        } else {
                            if ($request->type == 'credit') {
                                $transaction = $compte->transaction()->create([
                                    'designation' => $request->designation,
                                    'description' => $request->description,
                                    'currency' => $compte->currency,
                                    'credit' => $request->amount,
                                    'solde' => $count->balance() + $request->amount,
                                    'mount_lettre' => $request->mount_lettre,
                                    'userid' => $user->id,
                                    'method' => "wallet",
                                    'date' => $request->date,
                                ]);
                                return response()->json([
                                    "message" => "Le compte " . " " . $compte->count_number . " " . "est credité avec succès!",
                                    "code" => 200,
                                    "balance" => $count->balance(),
                                    "data" => CompteUserModel::with('membre', 'transaction.agent')->where('id', $compte->id)->first(),
                                    "transaction" => TransactionModel::with('agent', 'compte.membre')->find($transaction->id)
                                ], 200);
                            } else {
                                if ($compte->allowWithdraw($request->amount)) {
                                    $transaction = $compte->transaction()->create([
                                        'designation' => $request->designation,
                                        'description' => $request->description,
                                        'currency' => $compte->currency,
                                        'debit' => $request->amount,
                                        'solde' => $count->balance() - $request->amount,
                                        'mount_lettre' => $request->mount_lettre,
                                        'userid' => $user->id,
                                        'method' => "wallet",
                                        'date' => $request->date,
                                    ]);
                                    return response()->json([
                                        "message" => "Le compte " . " " . $compte->count_number . " " . "est debité avec succès!",
                                        "code" => 200,
                                        "balance" => $count->balance(),
                                        "data" => CompteUserModel::with('membre', 'transaction.agent')->where('id', $compte->id)->first(),
                                        "transaction" => TransactionModel::with('agent', 'compte.membre')->find($transaction->id)
                                    ], 200);
                                } else {
                                    return response()->json([
                                        "message" => "Solde insuffisant!",
                                        "code" => "402"
                                    ], 402);
                                }
                            }
                        }
                    } else {
                        return response()->json([
                            "message" => "Le type de transaction doit etre credit ou debit!",
                            "code" => "402"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => 'Le mot de passe est incorrect',
                        "code" => 422
                    ], 422);
                }
            } else {
                return response()->json([
                    "message" => 'Votre compte n\'est pas activé',
                    "code" => 422
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "C'est compte n'existe pas dans le système!",
                "code" => 402,
            ], 402);
        }
    }
    public function historiquetransaction_date(Request $request)
    {
        $request->validate([
            "date1" => "required",
            "date2" => "required",
        ]);
        if ($request->count_number == null) {
            $transaction = TransactionModel::with('compte.membre', 'agent')->whereBetween('date', [$request->date1, $request->date2])->get();
            return response()->json([
                "message" => "Historique des transactions de " . $request->date1 . "au" . $request->date2,
                "code" => "200",
                "data" => $transaction,
            ], 200);
        } else {
            $count = CompteUserModel::where('count_number', $request->count_number)->first();
            if ($count == true) {
                $transaction = TransactionModel::with('compte.membre', 'agent')->where('compteid', [$count->id])->get();
                return response()->json([
                    "message" => "Historique des transactions du compte " . $request->count_number,
                    "code" => "200",
                    "data" => $transaction,
                ], 200);
            } else {
                return response()->json([
                    "message" => "Ce numèro de compte" . " " . $request->count_number . " " . "n'est pas reconnue dans le système!",
                    "code" => "402"
                ], 402);
            }
        }
    }

    public function historiquetransaction_count_number(Request $request)
    {
        $request->validate([
            "count_number" => "required",
        ]);

        $count = CompteUserModel::where('count_number', $request->count_number)->first();
        if ($count == true) {
            $transaction = TransactionModel::with('compte', 'agent')->where('compteid', [$count->id])->get();
            return response()->json([
                "message" => "Historique des transactions du compte " . $request->count_number,
                "code" => "402",
                "data" => $transaction,
            ], 402);
        } else {
            return response()->json([
                "message" => "Ce numèro de compte" . " " . $request->count_number . " " . "n'est pas reconnue dans le système!",
                "code" => "402"
            ], 402);
        }
    }
}
