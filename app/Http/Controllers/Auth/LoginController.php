<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Clases necesaria para realizar la validacion con la libreria Auth de forma manual
use Illuminate\Support\Facades\Auth;
// Clase necesaria para recibir los parametros recibidos por Request
use Illuminate\Http\Request;
//Modelos utilizados en el controlador
use App\User;
use App\Perfil;
use App\Forma;
use App\Modulo;
use App\Valoracion;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /*
    * Funcion para validar la autentificacion del usuario, ademas del estado de usuario.
    */
     public function authenticate(Request $request)
    {
        //Se obtiene la informacion del usuario
        $user = User::where("email","=",$request['email'])->first();
        //si el usuario existe y su estado esta activo, continua con el proceso, si no se muestra un mensaje de error
        if(!is_null($user) && $user->estado != config('constantes.INACTIVO')){
            //obtenemos la informacion del perfil del usuario para tomar la ruta donde sera redireccionado
            $infoPerfil = Perfil::find($user->perfil);
            //Si el perfil existe y la ruta esta definida, se proceden a validar las credenciales, de lo contrario se genera un mensaje de error
            if(!is_null($infoPerfil) || isset($infoPerfil->url_redireccionamiento)){
                //Si el usuario y contraseña son correctos, se redirecciona al usuario, de lo contrario se genera un mensaje de error
                if (Auth::attempt([ 'email' => $request['email'], 'password' => $request['password'] ])) {                
                    
                    $forma = Forma::where("ruta",$infoPerfil->url_redireccionamiento)->first();                    
                    setcookie("Gestion", $forma->Descripcion);
                    $modulo = Modulo::find($forma->Modulo);
                    setcookie("Modulo",$modulo->Descripcion);
                    $Valoracion = Valoracion::where('Usuario',$user->id)
                                  ->limit(1)
                                  ->orderBy('id', 'desc')
                                  ->get();
                    if(isset($Valoracion[0]) && $user->perfil == config('constantes.ID_PERFIL_CLIENTE'))
                    {
                        return redirect('Valoraciones/'.$Valoracion[0]->id);
                    }
                    return redirect($infoPerfil->url_redireccionamiento);
                }else{            
                    return redirect('login')->withErrors(['El nombre de usuario que has introducido no pertenece a ninguna cuenta. Comprueba tu nombre de usuario y vuelve a intentarlo.']);
                }
            }else{
                return redirect('login')->withErrors(['El perfil asociado al usuario no existe.']);                
            }            
        }else{            
                return redirect('login')->withErrors(['El nombre de usuario que has introducido no pertenece a ninguna cuenta o esta inactivo. Comprueba tu nombre de usuario y vuelve a intentarlo.']);
        }
    }
}
