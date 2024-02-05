<?php

namespace App\Http\Controllers;

use App\Models\RessourceModel;
use Illuminate\Http\Request;

class RessourceController extends Controller
{
    public function create_ressource(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if (RessourceModel::where('name',$request->name)->exists()) {

            return response()->json([
                "message" => "Cette ressource existe déjà!",
                "code" => 402
            ], 402);
        } else {

            RessourceModel::create([
                'name' => $request->name,
            ]);
            return response()->json([
                "message" => "La ressource (" . $request->name . ") est créer avec succès!",
                "code" => 200,
            ]);
        }

    }

     public function update_ressource(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required|unique:t_resssources,name,' . $id,
            ],
            [
                'name.unique' => 'Cette ressource existe!'
            ]
        );

        $ressource = RessourceModel::where('id', $id)->first();
        if ($ressource) {

            $ressource->name = $request->name;
            $ressource->save();
            return response()->json([
                "message" => "La ressource (" . $request->name . ") est modifiée avec succès!",
                "code" => 200,
            ]);
        } else {
            return response()->json([
                "message" => "Cette ressourceid " . $id . " n'existe pas!",
                "code" => 402
            ], 402);
        }
    }

    public function detailressource($id){
        if(RessourceModel::where('id', $id)->exists()){
            return response()->json([
                "message" => "Liste des ressources",
                "code" => 200,
                "data" => RessourceModel::where('id', $id)->first(),
            ]);
        }else{
            return response()->json([
                "message" => "Cet id ".$id,"n'est pas reconnue dans le système!",
                "code" => 402,
            ],402);
        }

    }


    public function get_ressource()
    {
        return response()->json([
            "message" => "Liste des ressources",
            "code" => 200,
            "data" => RessourceModel::all(),
        ]);
    }
}
