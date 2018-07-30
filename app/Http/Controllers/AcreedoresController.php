<?php

namespace App\Http\Controllers;

use App\Acreedor;
use Illuminate\Http\Request;

class AcreedoresController extends Controller
{
    protected $forma = "ACRED";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $acreedores = Acreedor::all();
        return view('pages.Acreedores.index')->with('acreedores',$acreedores)->with('forma',$this->forma);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $acreedor = new Acreedor();
        return view('pages.Acreedores.form')->with('acreedor',$acreedor)->with('forma',$this->forma);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $acreedor = new Acreedor();
        $acreedor->fill($request->all());
        $acreedor->save();
        $acreedores = Acreedor::all();
        return view('pages.Acreedores.__list')->with('acreedores',$acreedores)->with('forma',$this->forma);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $acreedor  = Acreedor::find($id);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $acreedor  = Acreedor::find($id);
        return view('pages.Acreedores.form')->with('acreedor',$acreedor)->with('forma',$this->forma);
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

        $acreedor  = Acreedor::find($id);
        $acreedor->fill($request->all());
        $acreedor->save();
        $acreedores = Acreedor::all();
        return view('pages.Acreedores.__list')->with('acreedores',$acreedores)->with('forma',$this->forma);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $acreedor  = Acreedor::find($id);
        $acreedor->destroy();
        $acreedores = Acreedor::all();
        return view('pages.Acreedores.__list')->with('acreedores',$acreedores)->with('forma',$this->forma);
    }

    public function showContactos($id){
        $acreedor  = Acreedor::find($id);
        return view('pages.Acreedores.contactos')->with('acreedor',$acreedor)->with('forma',$this->forma);
    }

    public function createContactos($id){
        $acreedor  = Acreedor::find($id);
        return view('pages.Acreedores.contactos')->with('acreedor',$acreedor)->with('forma',$this->forma);
    }
}
