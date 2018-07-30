<?php

namespace App\Http\Controllers;

use App\Acreedor;
use App\Estudio;
use App\SolicitudConsulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Perfil;
use App\Forma;
use App\Modulo;
use App\Valoracion;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(isset(Auth::user()->id)){
            //obtenemos la informacion del perfil del usuario para tomar la ruta donde sera redireccionado
            $infoPerfil = Perfil::find(Auth::user()->perfil);
            //Si el perfil existe y la ruta esta definida, se proceden a validar las credenciales, de lo contrario se genera un mensaje de error
            if(!is_null($infoPerfil) || isset($infoPerfil->url_redireccionamiento)){
                //Si el usuario y contraseña son correctos, se redirecciona al usuario, de lo contrario se genera un mensaje de error

                    $forma = Forma::where("ruta",$infoPerfil->url_redireccionamiento)->first();                    
                    setcookie("Gestion", $forma->Descripcion);
                    $modulo = Modulo::find($forma->Modulo);
                    setcookie("Modulo",$modulo->Descripcion);
                    $Valoracion = Valoracion::where('Usuario',Auth::user()->id)
                                  ->limit(1)
                                  ->orderBy('id', 'desc')
                                  ->get();
                    if(isset($Valoracion[0]) && Auth::user()->perfil == config('constantes.ID_PERFIL_CLIENTE'))
                    {
                        return redirect('Valoraciones/'.$Valoracion[0]->id);
                    }
                    return redirect($infoPerfil->url_redireccionamiento);                
            }       
        }else{
            return view('layout.default');
        }         
    }

    public function showComments($type, $id){
        $commentables = ['estudio'=>new Estudio(), 'solicitud'=>new SolicitudConsulta(), 'acreedor'=>new Acreedor()];
        $comments = $commentables[$type]->find($id)->comments->reverse();
        return view('componentes.comentarios.comments')
            ->with('type', $type)
            ->with('comments',$comments)
            ->with('id',$id);
    }
    public function saveComment($type, $id, Request $request){
        $commentables = ['estudio'=>new Estudio(), 'solicitud'=>new SolicitudConsulta(), 'acreedor'=> new Acreedor()];
        $object = $commentables[$type]->find($id);
        $user = User::find(Auth::user()->id);
        $user->comment($object, $request->comment);
        $comments = $commentables[$type]->find($id)->comments->reverse();
        return view('componentes.comentarios.comments')
            ->with('type', $type)
            ->with('comments',$comments)
            ->with('id',$id);
    }

    public function showAjaxContent($type, Request $request){
        $types = [
            'desprendible' => 'pages.Estudio.components.__verDesprendible'
        ];
        return view($types[$type])->with($request->all());
    }
}
