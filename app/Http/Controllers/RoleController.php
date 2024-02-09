<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function create_role(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (RoleModel::where('name', $request->name)->exists()) {

            return response()->json([
                "message" => "Ce role existe déjà!",
                "code" => 402
            ], 402);
        } else {

            RoleModel::create([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "Le role (" . $request->name . ") est créer avec succès!",
                "code" => 200,
            ]);
        }
    }

    public function update_role(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:t_roles,name,' . $id,
        ], [
            'name.unique' => 'Ce role  exists déjà!'
        ]);

        $roles = RoleModel::where('id', $id)->first();
        if ($roles) {
            $roles->name = $request->name;
            $roles->save();
            return response()->json([
                "message" => "Le role (" . $request->name . ") est modifier avec succès!",
                "code" => 200,
            ]);
        } else {
            return response()->json([
                "message" => "Ce roleid " . $id . " n'existe pas!",
                "code" => 402
            ], 402);
        }
    }

    public function detailrole($id)
    {
        if (RoleModel::where('id', $id)->exists()) {
            return response()->json([
                "message" => "Liste des ressources",
                "code" => 200,
                "data" => RoleModel::where('id', $id)->first(),
            ]);
        } else {
            return response()->json([
                "message" => "Cet id " . $id, "n'est pas reconnue dans le système!",
                "code" => 402,
            ], 402);
        }
    }

    public function get_roles()
    {
        return response()->json([
            "message" => "Liste des roles",
            "code" => 200,
            "data" => RoleModel::where('deleted', 0)->get(),
        ]);
    }
    public function destroy($id)
    {
        $role = RoleModel::where('deleted', 0)->find($id);
        if ($role) {
            $role->deleted = 1;
            $role->save();
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
