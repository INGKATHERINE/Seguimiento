<?php
namespace App\Controllers;
use App\Models\mLogin;


class Login extends BaseController
{
    public function index()
    {
        echo view('Inicio');
    }

    public function loguear()
    {
        try {
        $conexion = new mLogin();
        $datos = $this->request->getPost();
        $result = $conexion->loguear($datos['usuario'],$datos['clave']);
        
        if(isset($result[0]))
        {
            session_start();
            $_SESSION['usuario'] = $result[0];
           print_r(json_encode(['result'=>'ok','direccion'=>base_url('/seguimiento')]));
        }
        else
        {
            print_r(json_encode(['result'=>'error','message'=>'Error']));
        }
        } catch (\Throwable $th) {
            print_r($th);
        }
        

    }

    public function cerraLogin()
    {
        session_start();
        session_destroy();
    }
}
