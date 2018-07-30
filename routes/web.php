<?php
//Grupo de rutas para los usuarios que acceden al sistema
use App\User;

Route::group(['middleware' => 'auth'], function ()
{
  Route::get('/', function () {
      return view('layout.default');
  });
/*********************
      ****** Modulos ******
*******************/
  Route::resource('Modulos','ModulosController');
  Route::post ('/editModulo', 'ModulosController@update');
  
  Route::post ( '/addModulo', 'ModulosController@create' );
  
  Route::post ( '/deleteModulo', 'ModulosController@destroy' );
  /*******************/
    /** Comentarios */
    Route::get ('/mostrarComentarios/{tipo}/{id}', 'HomeController@showComments');
    Route::post ('/guardarComentario/{tipo}/{id}', 'HomeController@saveComment');
  /*****************/

/*****************************************************************************/
/*********************
      ****** Mensajes ******
*******************/
  Route::resource('Mensajes','MensajesController');
  
  Route::post ('/editMensaje', 'MensajesController@update');
  
  Route::post ( '/addMensaje', 'MensajesController@create' );
  
  Route::post ( '/deleteMensaje', 'MensajesController@destroy' );
/*****************************************************************************/
/*********************
      ****** Estados ******
*******************/
  Route::resource('Estados','EstadosController');
  
  Route::post ('/editEstado', 'EstadosController@update');
  
  Route::post ( '/addEstado', 'EstadosController@create' );
  
  Route::post ( '/deleteEstado', 'EstadosController@destroy' );
/*****************************************************************************/
/*********************
      ****** Formas ******
*******************/
  Route::resource('Formas','FormasController');
  
  Route::post ('/editForma', 'FormasController@update');
  
  Route::post ( '/addForma', 'FormasController@create' );
  
  Route::post ( '/deleteForma', 'FormasController@destroy' );
/*****************************************************************************/
/*********************
      ****** Perfiles ******
*******************/
  Route::resource('Perfiles','PerfilesController');
  
  Route::post ('/editPerfil', 'PerfilesController@update');
  
  Route::post ( '/addPerfil', 'PerfilesController@create' );
  
  Route::post ( '/deletePerfil', 'PerfilesController@destroy' );
/*****************************************************************************/
/*********************
      ****** Permisos ******
*******************/
  Route::resource('Permisos','PermisosController');
  
  Route::post ('/editPermiso', 'PermisosController@update');
  
  Route::post ( '/addPermiso', 'PermisosController@create' );
  
  Route::post ( '/deletePermiso', 'PermisosController@destroy' );
/*****************************************************************************/
/*********************
      ****** Users ******
*******************/
  Route::resource('Usuarios','UsersController');
  Route::post('/editUsuario', 'UsersController@update');
  
  Route::post('/addUsuario', 'UsersController@create');
  
  Route::post('/deleteUsuario', 'UsersController@destroy');
  Route::post('/editPassword', 'UsersController@cambioPassword');
  Route::get('MiPerfil', 'UsersController@miPerfil');
  Route::post('actualizarPerfil','UsersController@actualizarPerfil');
  Route::post('cambiarFoto','UsersController@cambiarFoto');
  Route::post('cambioClave','UsersController@cambioClave');
  Route::resource('Clientes','UsersController@clientes');
  Route::post('editCliente', 'UsersController@updateCliente');
  Route::post('addCliente', 'UsersController@createCliente');
  Route::post('DesprendiblesCliente', 'UsersController@clavesDesprendibles');
/*****************************************************************************/
/*********************
      ****** Parametros ******
*******************/
  Route::resource('Parametros','ParametrosController');
   
  Route::post ('/editParametro', 'ParametrosController@update');
  
  Route::post ( '/addParametro', 'ParametrosController@create' );
  
  Route::post ( '/deleteParametro', 'ParametrosController@destroy' );
  Route::post ( '/calcularIvaCredito', 'ParametrosController@calcularIvaCredito' );
/*****************************************************************************/
/*********************
      ****** Sub Estados ******
*******************/
  Route::resource('SubEstados','SubEstadosController');
  
  Route::post ('/editSubEstado', 'SubEstadosController@update');
  
  Route::post ( '/addSubEstado', 'SubEstadosController@create' );
  
  Route::post ( '/deleteSubEstado', 'SubEstadosController@destroy' );
/*****************************************************************************/
  /*********************
      * Modulo Especial Simulaciones Juan Osorio **
*******************/
 /* Route::resource('Simulaciones','SimulacionesController');
  
  Route::post ('/editSimulaciones', 'SubEstadosController@update');
  
  Route::post ( '/addSubEstado', 'SubEstadosController@create' );
  
  Route::post ( '/deleteSubEstado', 'SubEstadosController@destroy' );*/
/*********************
      ****** Tesoreria ******
*******************/
  Route::resource('Tesoreria','TesoreriaController');
  
  Route::post ('/editTesoreria', 'TesoreriaController@update');
  Route::get('/generarPagoTesoreria', 'TesoreriaController@generarPago');

  Route::post ( '/addTesoreria', 'TesoreriaController@create' );
  
  Route::post ( '/deleteTesoreria', 'TesoreriaController@destroy');
  Route::post ('/Consulta', 'TesoreriaController@consulta');
  Route::get('DetalleTesoreria/{estudio}/{valoracion}/{cons?}',
  [
      'as'=>'Detalle',
      'uses'=>'TesoreriaController@detalle'
  ]);
  Route::post ('/actualizarTesoreria', 'TesoreriaController@actualizarTesoreria');
  
  /* GIROSS*/
  Route::get ('/TesoreriaPrueba/{id}', 'TesoreriaController@descargarArchivo');
  Route::post ('/addGiroCliente', 'TesoreriaController@adicionarGiroCliente');
  Route::post ('/Giros/Eliminar', 'TesoreriaController@EliminarGiro');
/*****************************************************************************/
/*********************
      ****** Entidades Bancarias ******
*******************/
  Route::resource('EntidadesBancarias','EntidadesBancariasController');
  
  Route::post ('/editBanco', 'EntidadesBancariasController@update');
  
  Route::post ( '/addBanco', 'EntidadesBancariasController@create' );
  
  Route::post ( '/deleteBanco', 'EntidadesBancariasController@destroy' );
/*****************************************************************************/
  /*********************
  ****** Entidades de Desembolso ******
*******************/
  Route::resource('EntidadesDesembolso','EntidadesDesembolsoController');
  
  Route::post ('/editEntidadDesembolso', 'EntidadesDesembolsoController@update');
  
  Route::post ( '/addEntidadDesembolso', 'EntidadesDesembolsoController@create' );
  
  Route::post ( '/deleteEntidadDesembolso', 'EntidadesDesembolsoController@destroy' );
/*****************************************************************************/
  /*********************
       ****** Tipos de Cuenta ******
*******************/
  Route::resource('TiposCuenta','TiposCuentaController');
  
  Route::post ('/editTipoCuenta', 'TiposCuentaController@update');
  
  Route::post ( '/addTipoCuenta', 'TiposCuentaController@create' );
  
  Route::post ( '/deleteTipoCuenta', 'TiposCuentaController@destroy' );
/*****************************************************************************/
  /*********************
       ****** Formas De Pago ******
*******************/
  Route::resource('FormasDePago','FormasDePagoController');
  
  Route::post ('/editFormaPago', 'FormasDePagoController@update');
  
  Route::post ( '/addFormaPago', 'FormasDePagoController@create' );
  
  Route::post ( '/deleteFormaPago', 'FormasDePagoController@destroy' );
/*****************************************************************************/
  /*********************
      ****** Cuentas Bancarias ******
*******************/
  Route::resource('CuentasBancarias','CuentasBancariasController');
  
  Route::post ('/editCuentaBancaria', 'CuentasBancariasController@update');
  
  Route::post ( '/addCuentaBancaria', 'CuentasBancariasController@create' );
  
  Route::post ( '/deleteCuentaBancaria', 'CuentasBancariasController@destroy' );
  
  Route::post ( '/listarCuentas', 'CuentasBancariasController@listarCuentas' );
/*****************************************************************************/
  /*********************
      ****** Valoraciones ******
*******************/
  Route::resource('acreedores','AcreedoresController');
  Route::post('acreedores/update/{id}','AcreedoresController@update');
 Route::resource('Valorar','ValoracionesController');
 Route::post('Valoracion','ValoracionesController@consumirDataCredito1');
 Route::resource('Valoraciones','ValoracionesController@consulaValoracion');
 Route::resource('Consultas','ValoracionesController@listarValoraciones');
 Route::resource('valoracion1','ValoracionesController@consumirDataCredito');
 Route::get('AdjuntosValoraciones/{id}', 'ValoracionesController@desplegarVistaAdjuntos');
 Route::resource('MisValoraciones','ValoracionesController@listarValoracionesCliente');
 Route::post ( 'Valoracion/updateEstudio', 'ValoracionesController@updateEstudio' );
 Route::post ( 'deleteValoracion', 'ValoracionesController@deleteValoracion' );


 
 /******************************* Gestion Obligaciones ************************/
 Route::get('GestionObligaciones/{id}','GestionObligacionesController@show');
 Route::get('GestionObligacionesValoracion/{id}','GestionObligacionesController@GestionObligacionesValoracion');
 Route::post ('GestionObligaciones/procesarObligaciones', 'GestionObligacionesController@procesarObligaciones');
 Route::post ('GestionObligaciones/procesarObligacionesDesprendible', 'GestionObligacionesController@procesarObligacionesDesprendible');
 Route::post ('GestionObligaciones/procesarObligacionesNuevas', 'GestionObligacionesController@procesarObligacionesNuevas');
 Route::post ('GestionObligaciones/ActualizarEst', 'GestionObligacionesController@actEstudio');

 /*****************************************************************************/
 
 
 
/*****************************************************************************/
/*********************
      ****** Comerciales ******
*******************/
/*****************************************************************************/
/*********************
      ****** Codigos Promocionales ******
*******************/
  Route::post('GenerarCodigo','CodigosPromocionalesController@generarCodigo');
  Route::post('consumirCodigo','CodigosPromocionalesController@consumirCodigo');
/*****************************************************************************/
/******************
    ******* Agenda ****** 
 **************/
  Route::resource('Agenda','AgendaController@detalleAgenda');
    
  Route::post ('/editAgenda', 'AgendaController@update');
  
  Route::post ( '/addAgenda', 'AgendaController@create' );
  
  Route::post ( '/dltAgenda', 'AgendaController@destroy' );
  
  //Route::post ( '/listarAgenda', 'AgendaController@listarAgendas' );
 
  Route::get('ConsumirServicio/{identificacion}/{apellido}','ApiController@consultaCliente');//->name('consumo')
  
  Route::resource('Pasos','PasosController');
  /*Proceso de solicitud de datos para la valoracion*/
  Route::post('/updateUser', 'BancoEntidadesController@updateDatosUsuario');  
  Route::resource('Datos','BancoEntidadesController');
  Route::post('/searchEntidad', 'BancoEntidadesController@getEntidades');
  Route::post('/getPreguntas', 'BancoEntidadesController@preguntas');  
  
  
  
  
/*********************************************** componente adjuntos  *******/
  Route::post('/uploadAdjuntos', 'Componentes\ComponentAdjuntosController@ControlUpload');/****/
  Route::get('/descargar/{id}', 'Componentes\ComponentAdjuntosController@descargar');/****/
  Route::get('/visualizar/{id}', 'Componentes\ComponentAdjuntosController@visualizar');/****/  
  Route::post('/EliminarAdjunto', 'Componentes\ComponentAdjuntosController@eliminar');/****/  
 /********************************************** fin componente adjuntos *****/
/*********************
       ****** Tipos de Adjuntos ******
*******************/
  Route::resource('TiposAdjuntos','TiposAdjuntoController');
  
  Route::post('editTipoAdjunto', 'TiposAdjuntoController@update');
  
  Route::post('addTipoAdjunto', 'TiposAdjuntoController@create');
  
  Route::post('/deleteTipoAdjunto', 'TiposAdjuntoController@destroy');
/*----------------------------------------------------------------------------------------------*/
  
  
  
  /*********************
    ****** Contactos ******
*******************/
  Route::resource('Contactos','ContactosController');
  Route::get('contactos/{type}/{id}', 'ContactosController@showContactos');
  Route::get('contactos-create/{type}/{id}', 'ContactosController@createContacto');
  Route::post('contactos-create/{type}/{id}', 'ContactosController@storeContacto');
  Route::post('editContacto', 'ContactosController@update');
  
  Route::post('addContacto', 'ContactosController@create');
  
  Route::post('/deleteContacto', 'ContactosController@destroy');
/*****************************************************************************/
  
  /**********************
        ******Gestion Oficina 
   ****************/
  Route::get('/GestionOficina/{id?}', 'GestionOficinaController@index');
  Route::post('dsAdjunto', 'GestionOficinaController@dsModalAdjuntos');
  Route::post('gesObligacion', 'GestionOficinaController@GestionObligacion');
  Route::post('prGestion', 'GestionOficinaController@GestionObligacion');
  Route::post('addTarea', 'GestionOficinaController@GestionAdicionarTarea');
  Route::post('searchClientes', 'GestionOficinaController@searchClientes');
  Route::post('searchConceptos', 'GestionOficinaController@searchConcepto');
  Route::post('gesTarea', 'GestionOficinaController@GestionTarea');
  Route::post('actTarea', 'GestionOficinaController@actAdjuntoTareas');
  /****************************************************************************/
  
  
  
  /*
   ************************************************************* Rutas para estudio
   */
  Route::post('Estudio/getDataJuridico', 'EstudioController@getDataJuridico');
  Route::get('/Estudio/{id}/{cons?}', 'EstudioController@desplegarEstudio');
  Route::get('/trackingEstudio/{id}', 'EstudioController@estudioTracking');
  Route::get('/calcularCupo', 'PagaduriasController@calcularCupo');
  Route::post('/Estudio/updObligacionPago', 'EstudioController@updatePago');
  Route::post('/Estudio/compraCartera', 'EstudioController@compraCartera');
  Route::post('/Estudio/updEntidadEstadoSaldo', 'EstudioController@updEntidadEstadoSaldo');
  Route::post('/Estudio/deleteObligacion', 'EstudioController@deleteObligacion');
  Route::post('/Estudio/actualizarCostos', 'EstudioController@actualizarCostos');
  Route::get('/Radicacion_Estudio', 'EstudioController@viewRadicacionEstudio');
  Route::get('/Adjuntos_Estudio/{id}/{idValoracion}', 'EstudioController@viewAdjuntosEstudio');
  Route::post('/Estudio/addIngresosAdicionales', 'EstudioController@addIngresosAdicionales');
  Route::post('/Estudio/delIngresosAdicionales', 'EstudioController@delIngresosAdicionales');
  Route::post('/Estudio/guardarEstudio', 'EstudioController@guardarEstudio');
  Route::post('/Estudio/aprobarEstudio', 'EstudioController@aprobarEstudio');
  Route::post('/Estudio/negarEstudio', 'EstudioController@negarEstudio');
  Route::post('/Estudio/guardarMiniCalculadora', 'EstudioController@guardarMiniCalculadora');
  Route::post('/Estudio/EliminarAdjuntos', 'EstudioController@EliminarAdjuntos');
  Route::post('/Estudio/GuardarFechas', 'EstudioController@GuardarFechas');
  Route::post('/Estudio/guardarFechasRadicacion', 'EstudioController@guardarFechasRadicacion');

    Route::post('Estudio/cambiarEstado', 'EstudioController@cambiarEstado');
    Route::post('Estudio/actualizarBancos', 'EstudioController@actualizarBancos');
  
  /*--------------------------------------------------------------------------------------------CARTERA--*/
  Route::get('/Cartera', 'CarteraController@dspLista');
  Route::get('/DetalleCartera/{id}', 'CarteraController@detalleCartera');
  Route::post('/Cartera/GuardarFechas', 'CarteraController@GuardarFechas');
  Route::post('/Cartera/guardarFechasRadicacion', 'CarteraController@guardarFechasRadicacion');  
  Route::get('/leerpdf', 'CarteraController@lecturaPDF');
  Route::get('/PagoMasivo', 'CarteraController@pagoMasivo');
  Route::post('/PagoMasivo/getData', 'CarteraController@getData');
  Route::get('/PazSalvo/{id}', 'CarteraController@generarPYS');
  Route::post('/GenerarCertificacion', 'CarteraController@generarCDD');
  Route::get('/VerCertificacion/{id}', 'CarteraController@consultarCertificacion');
  Route::post('/PagoMasivo/pagar', 'CarteraController@pagoMasivoPagar');
  Route::get('/Plan_de_pagos/{id}/{idVal}', 'CarteraController@dspTabla');
  Route::get('/Generar_Pdf/{id}/{idVal}', 'CarteraController@crearPdf');
  Route::get('/VerPDF/{id}', 'CarteraController@verPdf'); 
  Route::get('/EstadoCuenta/{id}', 'CarteraController@estadoCuenta');
  Route::post('Cartera/EliminarCertificacion', 'CarteraController@eliminarCertificacion');
  Route::post('Cartera/setComercialCartera', 'CarteraController@setComercialCartera');
  Route::post('Cartera/setBancoCartera', 'CarteraController@setBancoCartera');
  Route::post('Cartera/setValorAprobadoBanco', 'CarteraController@setValorAprobadoBanco');
  Route::post('Cartera/setEstadoCartera', 'CarteraController@setEstadoCartera');
  
  Route::get('/cronJobsCausacion', 'CarteraController@comparefechas');
  
  
  
  /*Rutas para probar cartera*/
    Route::get('/PruebaCausar/{id}/{fechaCausar}', 'CarteraPruebaController@causar');
    Route::get('/causarRapido/{id}/{mes}', 'CarteraPruebaController@causarRapido');
    Route::get('/BorrarTodo/{id}', 'CarteraPruebaController@BorrarTodo');
    Route::get('/PruebaPago/', 'CarteraPruebaController@pruebaPago');    
    Route::get('/PruebaCartera/{id}', 'CarteraPruebaController@listaCartera');
    Route::get('/totalDeuda/{id}', 'CarteraController@calcularCuandoDebeHoy');
    Route::get('/proyectarCertificacionesDeuda/{id}', 'CarteraController@proyectarCertificacionesDeuda');
        
    /*Rutas de cartera*/
    Route::post('/formPago', 'CarteraController@formPago');
    Route::get('/pruebaExcel', 'CarteraController@pruebaExcel');
    Route::get('/Devolucion/Reintegro/{id}', 'CarteraController@devolucion_reintegro');
  /*---------------------------------------------------------------------------------------------Migraciones*/
  Route::get('/GenerarCuotasProyectadas', 'ValoracionesController@migrarCuotasProyectadas');
  Route::get('/migrarAdjuntos', 'EstudioController@migrarAdjuntos');

  Route::resource('/pagadurias','PagaduriasController', ['as' => 'pagadurias']);
  Route::resource('/lideres','LideresController', ['as' => 'lideres']);
  
  /*Solicitudes de consulta*/
  Route::resource('/solicitudes','SolicitudConsultaController', ['as' => 'solicitudes']);
  Route::post('solicitudes/update', 'SolicitudConsultaController@update');
  Route::get('/municipios', 'SolicitudConsultaController@getMunicipio');
  Route::get('/solicitudes/ver-bancos/{id}', 'SolicitudConsultaController@mostrarBancos');
  Route::get('/solicitudes/ver-detalle/{id}', 'SolicitudConsultaController@detalleSolicitud');
  Route::post('solicitudes/devuelta', 'SolicitudConsultaController@solicitudDevuelta');

  Route::post('solicitudes/guardarBanco', 'SolicitudConsultaController@guardarBanco');
  Route::post('/valorarSolicitud/{id}', 'ValoracionesController@valorarSolicitud');
  
  /*Reportes Centrales de Riegos*/
  Route::get('reportes', 'ReporteController@reportesCentrales');
  Route::post('reportes/ajaxLstTable', 'ReporteController@ajaxLstTable');
  
  
  /*Adguntos generales*/
  
  Route::get('AdjuntosGenerales', 'AdjuntosGeneralesController@listAdjuntosGenerales');
  Route::get('/ajax-content/{view}', 'HomeController@showAjaxContent');

  Route::get('/agregar-obligacion/{id}', 'GestionObligacionesController@agregarObligacion');
  Route::post('/agregar-obligacion/{id}', 'GestionObligacionesController@guardarObligacion');
/*---------------------------------------------------------------------------------------------Fin Migraciones*/
}); //Fin grupo de rutas
Route::post ( '/BusquedaRegistro', 'ComercialesController@busquedaRegistro' );
Route::get('registro', function () {
      return view('pages.Valoraciones.registro');
  });
Route::post('/Iniciar', 'Auth\LoginController@authenticate');
Route::get('/home', 'HomeController@index');
Route::get('/valoracion', function(){
    return view('layouts-client.valoracion.index');
});
Auth::routes();

Route::get('/pruebaFuncion', 'EstudioController@validateCertificadosEnCompras');
Route::get('/pruebaeval', 'Componentes\ComponentAdjuntosController@pruebaeval');
Route::get('/prueba/{id}', 'ValoracionesController@ConsultaXML');
Route::get('/formulario/{id}', 'ValoracionesController@ConsultaXML');

Route::get('/formulario-registro/{id}', 'UsersController@getFormularioSolicitud');

Route::post('/formulario-registro/{id}', 'UsersController@updateFormularioData')->name('update-solicitud-data');
Route::resource('comerciales_vtm','ComercialesController');
