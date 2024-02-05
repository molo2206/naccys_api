<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationModel;
use Illuminate\Http\Request;
use League\Config\Configuration;

class ConfigurationController extends Controller
{
    public function create_infos_app(Request $request)
    {

        $request->validate([
            'organisation_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'rccm' => 'required',
            'idnat' => 'required',
            'num_impot' => 'required',
        ]);

        $conf = ConfigurationModel::first();
       
            if ($conf == null) {
                ConfigurationModel::create([
                    'organisation_name' => $request->organisation_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'rccm'  => $request->rccm,
                    'idnat' => $request->idnat,
                    'num_impot' => $request->num_impot,
                ]);
                return response()->json([
                    "message" => "success",
                    "code" => 200,
                    "data" => ConfigurationModel::where('organisation_name', $request->organisation_name)->first(),
                ], 200);
            } else {
                $conf->organisation_name = $request->organisation_name;
                $conf->email = $request->email;
                $conf->phone = $request->phone;
                $conf->rccm  = $request->rccm;
                $conf->idnat = $request->idnat;
                $conf->num_impot = $request->num_impot;
                $conf->update();
                  return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('organisation_name', $request->organisation_name)->first(),
            ], 200);
            }
          
        
    }

    public function update_infos_organisation(Request $request)
    {

        $conf = ConfigurationModel::first();
        if ($conf == null) {
            return response()->json([
                "message" => "Cet id n'est pas recconue dans le système!",
                "code" => 402,
            ], 402);
        } else {

            if ($request->organisation_name == null) {
                $conf->organisation_name = $conf->organisation_name;
            } else {
                $conf->organisation_name = $request->organisation_name;
            }

            if ($request->email == null) {
                $conf->email = $conf->email;
            } else {
                $conf->email = $request->email;
            }

            if ($request->phone == null) {
                $conf->phone = $conf->phone;
            } else {
                $conf->phone = $request->phone;
            }
            if ($request->rccm == null) {
                $conf->rccm = $conf->rccm;
            } else {
                $conf->rccm = $request->rccm;
            }

            if ($request->idnat == null) {
                $conf->idnat = $conf->idnat;
            } else {
                $conf->idnat = $request->idnat;
            }

            if ($request->num_impot == null) {
                $conf->num_impot = $conf->num_impot;
            } else {
                $conf->num_impot = $request->num_impot;
            }
            $conf->update();
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $conf->id)->first(),
            ], 200);
        }
    }

    public function detail_info($id)
    {
        return response()->json([
            "message" => "success",
            "code" => 200,
            "data" => ConfigurationModel::where('id', $id)->first(),
        ], 200);
    }

    public function get_infos_organisation()
    {
        return response()->json([
            "message" => "success",
            "code" => 200,
            "data" => ConfigurationModel::first(),
        ], 200);
    }

    public function create_interet (Request $request)
    {
        $request->validate([
            'taux_interet' => 'required',
            'taux_penalite' => 'required',
        ]);

        $conf = ConfigurationModel::first();
        if ($conf == null) {
            ConfigurationModel::create([
                'taux_interet' => $request->taux_interet,
                'taux_penalite' => $request->taux_penalite,
            ]);
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('organisation_name', $request->id)->first(),
            ], 200);
        } else {
            $conf->taux_interet = $request->taux_interet;
            $conf->taux_penalite = $request->taux_penalite;
            $conf->update();
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $conf->id)->first(),
            ], 200);
        }
    }

    public function create_logo_fiveicon(Request $request)
    {
        $logo = ImageController::uploadImageUrl($request->logo, '/uploads/agent/');
        $fiveicone = ImageController::uploadImageUrl($request->fiveicone, '/uploads/agent/');
        $conf = ConfigurationModel::first();
        if ($conf == null) {
            ConfigurationModel::create([
                'logo' => $logo,
                'fiveicone' => $fiveicone,
            ]);
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $request->id)->first(),
            ], 200);
        } else {
            if ($logo == null) {
                $conf ->logo = $conf->logo;
            } else {
                $conf->logo = $logo;
            }
            if ($fiveicone == null) {
                $conf ->fiveicone = $conf->fiveicone;
            } else {
                $conf->fiveicone = $fiveicone;
            }
            $conf->update();
            return response()->json([
                "message" => "success",
                "code" => 200,
                "data" => ConfigurationModel::where('id', $conf->id)->first(),
            ], 200);
        }
    }
}
