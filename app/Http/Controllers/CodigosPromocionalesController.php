<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\CodigoPromocional;

class CodigosPromocionalesController extends Controller
{
    public function generarCodigo(Request $request)
    {
        $CodigoPromocional = new CodigoPromocional();
        $CodigoPromocional->Codigo = strtolower(str_random(6));
        $CodigoPromocional->Usuario = Auth::user()->id;
        $CodigoPromocional->save();

        return response()->json(['Codigo' => $CodigoPromocional->Codigo]);
    }

    public function consumirCodigo(Request $request)
    {
        session(['valido' => true]);
        $CodigoPromocional = CodigoPromocional::where('Codigo',$request->CodigoPromocional)->first();
        if(is_null($CodigoPromocional) || !is_null($CodigoPromocional->Cliente))
        {
            session(['valido' => false]);
            return response()->json(['valido' => false]);
        }
        $CodigoPromocional->Cliente = Auth::user()->id;
        $CodigoPromocional->save();
        
        return response()->json(['valido' => true]);
    }
}