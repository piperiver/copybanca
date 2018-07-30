<?php

namespace App\Http\Controllers;

use App\Pagaduria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Librerias\UtilidadesClass;
use stdClass;

class PagaduriasController extends Controller
{
    protected $forma = "PAGAD";

    public function index()
    {
        if (!UtilidadesClass::ValidarAcceso($this->forma)) {
            return view('errors.401');
        }
        $data = new Pagaduria();
        return view('pages.Pagaduria.index')->with('pagadurias', $this->listarPagadurias())->with('forma', $this->forma)->with('pagaduria_object', new Pagaduria())->with($data->parameters());
    }

    public function store(Request $request)
    {
        $pagaduria = new Pagaduria($request->all());
        $condiciones = ['nombre' => 'required|min:3|unique:Pagadurias',
            'tipo' => 'required|max:1000'];

        $mensajes = ['required' => 'Campo :attribute es Obligatorio.',
            'max' => 'Campo :attribute no permite un numero mayor a  :max',
            'unique' => 'El Nombre esta retido esta repetido.'];

        $validacion = \Validator::make($request->all(), $condiciones, $mensajes);

        if ($validacion->fails()) {
            return response()->json(['errores' => $validacion->fails(), 'Mensaje' => $validacion->errors()->all()]);
        }

        $pagaduria = new Pagaduria($request->all());
        $pagaduria->save();
        $tabla = $this->tabla($this->listarPagadurias());

        return response()->json(['Mensaje' => 'El registro se ha guardado.',
            'tabla' => $tabla]);
    }

    public function edit($id)
    {
        $pagaduria = Pagaduria::find($id);
        $view = view('pages.Pagaduria.form')->with('pagaduria_object', $pagaduria)->with($pagaduria->parameters())->render();
        return $view;
    }

    public function update(Request $request, $id)
    {
        $pagaduria = Pagaduria::find($id);
        $pagaduria->fill($request->all());
        $pagaduria->save();
        $tabla = $this->tabla($this->listarPagadurias());

        return response()->json(['Mensaje' => 'El registro se ha modificado.',
            'tabla' => $tabla]);
    }

    public function destroy($id)
    {
        $pagaduria = Pagaduria::find($id);
        $pagaduria->delete();
        $tabla = $this->tabla($this->listarPagadurias());
        return response()->json(['Mensaje' => 'El registro se ha Eliminado.',
            'tabla' => $tabla]);
    }

    public function listarPagadurias()
    {
        return Pagaduria::orderBy('nombre', 'ASC')->get();
    }

    public function tabla($pagadurias)
    {
        $tabla = "<table class='table table-striped table-bordered table-hover table-checkable order-column text-center' id='tabla'>
                    <thead>
                      <tr>
                          <th> Código </th>
                          <th> Nombre </th>
                          <th> Tipo de pagaduría </th>
                          <th> Tipo de pagaduría </th>";
        if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar") || UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
            $tabla .= "<th> Acción </th>";
        }
        $tabla .= "</tr>
                    </thead>
                    <tbody>";
        foreach ($pagadurias as $pagaduria) {
            $tabla .=
                "<tr id='" . $pagaduria->codigo . "'>
                        <td>" . $pagaduria->codigo . "</td>
                        <td>" . $pagaduria->nombre . "</td>
                        <td>" . $pagaduria->tipo . "</td>
                        <td>" . $pagaduria->created_at->format('d/m/Y') . "</td>";

            if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar") || UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
                $tabla .= "<td>";
                if (UtilidadesClass::ValidarAcceso($this->forma, "Actualizar")) {
                    $tabla .= "<a class='btn btn-icon-only yellow-gold update' data-toggle='modal' data-update_url=" . url('pagadurias', ['id' => $pagaduria->id, 'edit' => 'edit']) . " >
                                                <i class='fa fa-edit'></i>
                                           </a>";
                }
                if (UtilidadesClass::ValidarAcceso($this->forma, "Eliminar")) {
                    $tabla .= "<a href='' id='lkDelete' name='lkDelete' class='btn btn-icon-only red' data-delete-url='" . url('pagadurias', ['id' => $pagaduria->id]) . "' data-toggle='modal' data-id='" . $pagaduria->id . "'>
                                                <i class='fa fa-close'></i>
                                           </a>";
                }
                $tabla .= "</td>";
            }
            $tabla .= "</tr>";
        }
        $tabla .= "</tbody></table>";
        return $tabla;
    }

    public function calcularCupo(Request $request){
        $pagaduria = Pagaduria::find($request->pagaduria);
        return response()->json($pagaduria->calcularCupo($request->ingreso,$request->egreso, $request->regimen_especial));
    }
}