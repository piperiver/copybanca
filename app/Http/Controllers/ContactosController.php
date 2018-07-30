<?php

namespace App\Http\Controllers;

use App\Acreedor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contacto;
use App\Librerias\UtilidadesClass;

class ContactosController extends Controller
{
    protected $forma = 'CONTA';
    
    public function index()
    {
        if(!UtilidadesClass::ValidarAcceso($this->forma)){
            return view('errors.401');    
        }
        $Contactos = Contacto::orderBy('Nombre', 'ASC')->get();

        return view('pages.Contactos.index')->with('Contactos', $Contactos)->with('forma', $this->forma);
    }

    public function create(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Entidad' => 'required|max:255',
                        'Telefono' => 'required|max:20',
                        'Correo' => 'required|max:255|email',
                        'Area' => 'required|max:255'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Contacto = new Contacto($request->all());
        $Contacto->save();

        $Contactos = Contacto::orderBy('Nombre', 'ASC')->get();
        $tabla = $this->tabla($Contactos);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function update(Request $request)
    {
        $condiciones = ['Nombre' => 'required|max:255',
                        'Entidad' => 'required|max:255',
                        'Telefono' => 'required|max:20',
                        'Correo' => 'required|max:255|email',
                        'Area' => 'required|max:255'];
        
        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
                     'max' => 'Campo :attribute no permite un numero mayor a  :max'];
        
        $validacion = \Validator::make($request->all(),$condiciones,$mensajes);

        if ($validacion->fails())
        {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $Contacto = Contacto::find($request->id);
        $Contacto->fill($request->all());
        $Contacto->save();
        $Contactos = Contacto::orderBy('Nombre', 'ASC')->get();
        $tabla = $this->tabla($Contactos);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function destroy(Request $request)
    {
        $Contacto = Contacto::find($request->id);
        $Contacto->delete();

        $Contactos = Contacto::orderBy('Nombre', 'ASC')->get();
        $tabla = $this->tabla($Contactos);

        return response()->json(['Mensaje' => 'El registro se ha Guardado.',
                                 'tabla' => $tabla]);
    }

    public function tabla($Contactos)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                            <th> Nombre </th>
                            <th> Entidad </th>
                            <th> Telefono </th>
                            <th> Acción </th>
                      </tr>
                    </thead>
                    <tbody>";
                    foreach($Contactos as $Contacto)
                    {
                        $tabla .=
                        "<tr id='".$Contacto->id."'>
                        <td>". $Contacto->Nombre ."</td>
                        <td>". $Contacto->Entidad ."</td>
                        <td>". $Contacto->Telefono ."</td>
                        <td>
                            <a href='' id='lkVer' name='lkVer' class='btn btn-icon-only green' data-toggle='modal'
                                data-nombre='".$Contacto->Nombre."' data-entidad='".$Contacto->Entidad."' data-cargo='".$Contacto->Cargo."' 
                                data-telefono='".$Contacto->Telefono."' data-correo='".$Contacto->Correo."' data-area='".$Contacto->Area."'>
                                    <i class='fa fa-plus'></i>
                            </a>";
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Actualizar"))
                           {
                                $tabla .=
                                "<a href='' id='lkEdit' name='lkEdit' class='btn btn-icon-only yellow-gold' data-toggle='modal' 
                                    data-id='".$Contacto->id."' data-nombre='".$Contacto->Nombre."' data-entidad='".$Contacto->Entidad."' data-cargo='".$Contacto->Cargo."' data-telefono='".$Contacto->Telefono."' data-correo='".$Contacto->Correo."' data-area='".$Contacto->Area."'>
                                        <i class='fa fa-edit'></i>
                                </a>";
                            }
                            if(UtilidadesClass::ValidarAcceso($this->forma,"Eliminar"))
                            {
                                $tabla .=
                                "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-toggle='modal' data-id='".$Contacto->id."'>
                                    <i class='fa fa-close'></i>
                                 </a>";
                            }
                            $tabla .= "</td>";
                        
                     $tabla .= "</tr>";
                }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    public function showContactos($type, $id){
        $contactables = ['acreedores'=>new Acreedor()];
        $contacts = $contactables[$type]->find($id)->contactos;
        return view('pages.Contactos.__list')->with('type',$type)->with('id',$id)->with('contacts', $contacts);

    }

    public function createContacto($type, $id){
        return view('pages.Contactos.__form')->with('type',$type)->with('id',$id);
    }

    public function storeContacto($type, $id, Request $request){
        $contactables = ['acreedores'=>new Acreedor()];
        $contacto = new Contacto();
        $contacto->fill($request->all());
        $contacts = $contactables[$type]->find($id)->contactos()->save($contacto);
        $contacts = $contactables[$type]->find($id)->contactos;
        return view('pages.Contactos.__list')->with('type',$type)->with('id',$id)->with('contacts', $contacts);
    }
}