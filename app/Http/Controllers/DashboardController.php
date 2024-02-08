<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TransactionModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->date_debut && $request->date_fin) {
            $depot_usd = TransactionModel::transaction_usd()->whereBetween('date', [$request->date_debut, $request->date_fin])->sum('credit');
            $retrait_usd = TransactionModel::transaction_usd()->whereBetween('date', [$request->date_debut, $request->date_fin])->sum('debit');
            $membre = User::whereHas('count', function ($q) {
                $q->where('deleted', 0);
            })->count();
            $transaction_cdf = TransactionModel::transaction_cdf()->whereBetween('date', [$request->date_debut, $request->date_fin]);

            $depots = TransactionModel::select(
                DB::raw('sum(credit) as amount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as month")
            )
                ->where('currency', $request->get('currency') ? $request->get('currency') : "$")
                ->whereBetween('date', [$request->date_debut, $request->date_fin])
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();
            $retraits = TransactionModel::select(
                DB::raw('sum(debit) as amount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as month")
            )
                ->where('currency', $request->get('currency') ? $request->get('currency') : "$")
                ->whereBetween('date', [$request->date_debut, $request->date_fin])
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            $data = [
                "usd" => [
                    "depot" => $depot_usd,
                    "retrait" => $retrait_usd,
                    "pret" => 0,
                ],
                "cdf" => [
                    "depot" => $transaction_cdf->sum('credit'),
                    "retrait" => $transaction_cdf->sum('debit'),
                    "count_depot" => $transaction_cdf->count('credit'),
                    "count_retrait" => $transaction_cdf->count('debit')
                ],
                "membres" => $membre,
                "line_chart" => [
                    "depots" => $depots,
                    "retraits" => $retraits
                ]
            ];
            return response()->json([
                "data" => $data
            ], 200);
        } else {
            $depot_usd = TransactionModel::transaction_usd()->whereYear('date', date('Y'))->sum('credit');
            $retrait_usd = TransactionModel::transaction_usd()->whereYear('date', date('Y'))->sum('debit');
            $membre = User::whereHas('count', function ($q) {
                $q->where('deleted', 0);
            })->count();
            $transaction_cdf = TransactionModel::transaction_cdf()->whereYear('date', date('Y'));

            $depots = TransactionModel::select(
                DB::raw('sum(credit) as amount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as month")
            )
                ->where('currency', $request->get('currency') ? $request->get('currency') : "$")
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();
            $retraits = TransactionModel::select(
                DB::raw('sum(debit) as amount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as month")
            )
                ->where('currency', $request->get('currency') ? $request->get('currency') : "$")
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            $data = [
                "usd" => [
                    "depot" => $depot_usd,
                    "retrait" => $retrait_usd,
                    "pret" => 0,
                ],
                "cdf" => [
                    "depot" => $transaction_cdf->sum('credit'),
                    "retrait" => $transaction_cdf->sum('debit'),
                    "count_depot" => $transaction_cdf->count('credit'),
                    "count_retrait" => $transaction_cdf->count('debit')
                ],
                "membres" => $membre,
                "line_chart" => [
                    "depots" => $depots,
                    "retraits" => $retraits
                ]
            ];
            return response()->json([
                "data" => $data
            ], 200);
        }
    }
}
