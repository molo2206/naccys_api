<?php
use App\Http\Controllers\CompteUserController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\RessourceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolesPersmissionUserController;
use App\Http\Controllers\UserContoller;
use App\Models\RessourceModel;
use App\Models\RolesPersmissionUserModel;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/user/login', [UserContoller::class, 'login']);
Route::post('/user/lost_pswd', [UserContoller::class, 'Lost_pswd']);
Route::post('/user/reinitializer_pswd', [UserContoller::class, 'reinitialiser_pswd']);
Route::post('/user/askcodevalidation', [UserContoller::class, 'askcodevalidateion']);
Route::post('/user/verify_otp', [UserContoller::class, 'verify_otp']);
Route::get('/configuration/get_infos_organisation',[ConfigurationController::class,'get_infos_organisation']);

Route::group(['middleware' => ['auth:sanctum']], function () {
Route::post('/user/changepassword', [UserContoller::class, 'changePswdProfil']);
Route::post('/user/editprofil', [UserContoller::class, 'editProfile']);
Route::post('/user/updateuser/{id}', [UserContoller::class, 'UpdateUser']);
Route::post('/user/editimage', [UserContoller::class, 'editImage']);
Route::get('/user/detailuser/{id}', [UserContoller::class, 'getuserId']);
Route::get('/user/get_current_user', [UserContoller::class, 'getuser']);
Route::get('/user/getusers', [UserContoller::class, 'getuser']);
Route::get('/user/getmembers', [UserContoller::class, 'getMembre']);


Route::post('/user/new_agent', [UserContoller::class, 'create_agent']);
Route::post('/user/create_member', [UserContoller::class, 'create_membre']);
Route::post('/permission/create_permission', [RolesPersmissionUserController::class, 'permission']);
Route::post('/ressource/create_ressource' , [RessourceController::class, 'create_ressource']);
Route::get('/ressource/get_ressources', [RessourceController::class, 'get_ressource']);
Route::post('/ressource/update_ressource/{id}', [RessourceController::class, 'update_ressource']);
Route::post('/role/new_role', [RoleController::class, 'create_role']);
Route::post('/role/update_role/{id}', [RoleController::class, 'update_role']);
Route::get('/role/get_roles', [RoleController::class, 'get_roles']);
Route::get('/typeperson/get_type_personne', [UserContoller::class, 'get_type_personne']);
Route::get('/ressource/detailressource/{id}' ,[RessourceController::class, 'detailressource']);
Route::get('/role/detailrole/{id}', [RoleController::class, 'detailrole']);

// Transaction and count user
Route::post('/count/create_count',[CompteUserController::class, 'create_count']);
Route::post('/transaction/make_transaction',[TransactionController::class, 'make_transaction']);
Route::post('/transaction/get_historique_by_date',[TransactionController::class, 'historiquetransaction_date']);
Route::post('/transaction/get_historique_by_count',[TransactionController::class, 'historiquetransaction_count_number']);
Route::get('/member/get_count_member',[UserContoller::class, 'getCountmember']);
Route::post('/member/update_member/{id}',[UserContoller::class, 'UpdateMembre']);
Route::get('/member/getcountmember/{id}',[UserContoller::class, 'getCountmember']);
Route::post('/count/locked_count/{id}',[CompteUserController::class, 'locked_count']);
Route::get('/count/getcount_bycount_number/{count}',[CompteUserController::class, 'getmemberbycount_number']);
Route::post('/count/searchcount', [CompteUserController::class, 'recherche']);

Route::post('/configuration/create_infos_app',[ConfigurationController::class, 'create_infos_app']);
Route::post('/configuration/update_infos_organisation',[ConfigurationController::class,'update_infos_organisation']);
Route::get('/configuration/detail_info/{id}',[ConfigurationController::class,'detail_info']);
Route::post('/configuration/create_interet',[ConfigurationController::class,'create_interet']);
Route::post('/configuration/create_logo_fiveicon',[ConfigurationController::class,'create_logo_fiveicon']);


});
