<?php

namespace App\Http\Controllers;

use App\Models\RolesPersmissionUserModel;
use App\Models\User;
use Illuminate\Http\Request;

class RolesPersmissionUserController extends Controller
{
    public function permission(Request $request)
    {
        $request->validate([
            'userid' => 'required',
            'permissions' => 'required'
        ]);
        $user = User::where('id', $request->userid)->first();
        if ($user) {
            $user->permissions()->detach();
            foreach ($request->permissions as $item) {
                $user->permissions()->attach([$item['ressourceid'] =>
                [
                    'create' => $item['create'],
                    'update' => $item['update'],
                    'delete' => $item['delete'],
                    'read'   => $item['read'],
                    'status' => $item['status'],
                ]]);
            }
            return response()->json([
                "message" => "Permission accordée avec succès!",
                "code" => 200,
                "data" => User::with('roles', 'type', 'permissions')->where('id', $request->userid)->first(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Cet utilisateur n'est pas reconnue dans le système!",
                "code" => 402,
            ], 402);
        }
    }
}
