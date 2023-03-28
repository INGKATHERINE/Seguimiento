<?php namespace App\Models;

use CodeIgniter\Model;


class mLogin extends Model
{
    function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function loguear($usuario,$clave)
    {
        $builder = $this->db->table('login');
        $builder->where('usuario',$usuario);
        $builder->where('pwd',$clave);
        return $builder->get()->getResultArray();
    }
}