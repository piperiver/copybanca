<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');

Route::post('/userPruebaApi', "apiEvidenteController@consumoVerificar");

Route::post('/pruebaConsumo', function(Request $request){
    return "Consulta exitosa ".$request->nombre;
});

//Route::resource('Valoraciones','ValoracionesController',['only' => ['index']]);
//Route::post('Valoracion','ValoracionesController',['only' => ['consumirDataCredito']]);
Route::post('Valoracion','ValoracionesController@consumirDataCredito');
Route::resource('Prueba','ValoracionesController@llamadoApi');
Route::post('registro','ValoracionesController@registroCliente');
Auth::routes();