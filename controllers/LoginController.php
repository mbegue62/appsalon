<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {
        $alertas =[];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $auth = new Usuario($_POST);

            $alertas=$auth->validarLogin();
            if(empty($alertas)) {
            // Comprobar que existe el Usuario
            $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    // Verificar el usuario
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar usuario
                        if(!isset($_SESSION)) {
                            session_start();
                        }
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = TRUE;

                        // Redireccionamiento

                        if($usuario->admin==="1"){
                            $_SESSION['admin'] = $usuario->admin ?? NULL;

                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }

                        debuguear($_SESSION);
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            
            }
        $alertas = Usuario::getAlertas();
        }
        $router->render('auth/login', [
            'alertas'=> $alertas

        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
           if(empty($alertas)) {
            $usuario= Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === "1") {
                    // Generar un Token

                    $usuario->crearToken();
                    $usuario->guardar();

                // Enviar el email
                    $emai = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $emai->enviarInstrucciones();
                // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu E-mail');
                } else {
                    Usuario::setAlerta('error', 'El usuario NO EXISTE o NO esta CONFIRMADO');
                   
                }
            
           }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas'=>$alertas
        ]);
    }

    public static function recuperar(Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $error = false;

        // Buscar un Usuario por su token

        $usuario= Usuario::where('token', $token);

            if(empty($usuario)) {
                Usuario::setAlerta('error', 'Token no Válido');
                $error = true;
            }

            if($_SERVER['REQUEST_METHOD']==='POST') {
                // Leer el nuevo password y validarlo
                $password = new Usuario($_POST);
                $alertas=$password->validarPassword();
               if(empty($alertas)) {
                    $usuario->password=null;
                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token=null;
                    $resultado=$usuario->guardar();
                    if($resultado) {
                        header('Location: /');
                    }
                    
               }
            }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas'=>$alertas,
            'error'=>$error
        ]);
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;
        $alertas =[];
if($_SERVER['REQUEST_METHOD']==='POST') {

    $usuario->sincronizar($_POST);
    $alertas=$usuario->validarNuevaCuenta();
    if(empty($alertas)){
        // Verificar que el usuario no este registrado
        $resultado=$usuario->existeUsuario();
        if($resultado->num_rows) {
            $alertas = Usuario::getAlertas();
        } else { 
            // hashear el password
            $usuario->hashPassword();
           // Gnerar un token único
           $usuario->crearToken();
           
           $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
           $email->enviarConfirmacion();

           $resultado = $usuario->guardar();
           if ($resultado) {
            header('Location: /mensaje');
           }
                      
        }
    }
    
    
    
}

        $router->render('auth/crear-cuenta', [
            'usuario'=>$usuario,
            'alertas'=>$alertas
        ]);
    }
    public static function mensaje (Router $router) {
        $router->render('auth/mensaje');
    }
    public static function confirmar (Router $router) {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no Válido');
        } else {
            // Mostrar a usuario confirmado
            $usuario->confirmado=1;
            $usuario->token = NULL;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Token Válido, cuenta comprobada');
        }
        // Obtener alertas
        $alertas=Usuario::getAlertas();
        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [

            'alertas' => $alertas
        ]);
    }
}