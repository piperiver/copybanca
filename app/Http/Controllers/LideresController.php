<?php

namespace App\Http\Controllers;

use App\Librerias\UtilidadesClass;
use App\Providers\AuthServiceProvider;
use App\SolicitudConsulta;
use App\SolicitudMasiva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LideresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $forma = "LIDER";
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');
        }
        if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar")){
            $solicitudes = SolicitudMasiva::all();
        }else{
            $solicitudes = SolicitudMasiva::where('user_id', Auth::user()->id)->get();
        }
        return view('pages.LideresComerciales.index')->with('forma',$this->forma)->with('solicitudes', $solicitudes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $objUtilidades = new UtilidadesClass();
        $solicitud = new SolicitudMasiva();
        $solicitud->nombre = strtoupper(substr(Auth::user()->nombre, -3)).date("ymd");
        $solicitud->comentario = $request->comentario;
        $solicitud->user_id = Auth::user()->id;
        $solicitud->save();
        $file = $request->file('archivo');
        $NombreOriginal = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $id = $objUtilidades->registroAdjunto($solicitud->id,'solicitudes_consulta',$NombreOriginal,$extension,config("constantes.CARGUE_MASIVO_LIDER"), config("constantes.MDL_VALORACION"));
        $subido =  \Storage::disk('adjuntos')->put($id, \File::get($file));;
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
