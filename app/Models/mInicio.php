<?php namespace App\Models;

use CodeIgniter\Model;


class mInicio extends Model
{
    function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function obterAsuntos()
    {
        $builder =  $this->db->table('asunto');
        return $builder->get()->getResultArray();
    }

    public function obterDocumento()
    {
        $builder =  $this->db->table('documento d');
        $builder->where('estado=0');
        return $builder->get()->getResultArray();
    }

    public function obterMedio()
    {
        $builder =  $this->db->table('medio');
        return $builder->get()->getResultArray();
    }

    public function obterEstado()
    {
        $builder =  $this->db->table('estado');
        return $builder->get()->getResultArray();
    }

    public function obterRemitenteDestinatario()
    {
        $builder =  $this->db->table('remitente_destinatario');
        return $builder->get()->getResultArray();
    }

    public function actualizarFila($nIdDocumento,$aDocumento)
    {
        $builder =  $this->db->table('documento');
        $builder->set($aDocumento);
        $builder->where('id_documento',$nIdDocumento);
        $builder->update();
        return $this->db->affectedRows();

    }

    public function guardarFila($aDocumento)
    {
        $builder =  $this->db->table('documento');
        $builder->set($aDocumento);
        $builder->insert();
        return $this->db->affectedRows();
    }

    public function eliminarFila($nIdDocumento)
    {
        $builder =  $this->db->table('documento');
        $builder->where('id_documento',$nIdDocumento);
        $builder->set([
            "estado"=>1,
        ]);
        $builder->update();
        return $this->db->affectedRows();
    }

    public function buscador($sbuscar)
    {
        $builder = $this->db->table('documento d');
        $builder->where("(d.fecha like '%{$sbuscar}%' or d.observacion like '%{$sbuscar}%' or d.n_registro like '%{$sbuscar}%') and d.estado=0");
        return $builder->get()->getResultArray();
        
    }

    public function filtrar($sFiltrar,$rfiltrar,$dFiltrar)
    {
        $builder = $this->db->table('documento d');
        $builder->join('asunto a','d.id_asunto=a.id_asunto');
        $builder->where("d.estado=0");
        if (isset($rfiltrar)&&$rfiltrar!=null&&$rfiltrar!="null") {
            $builder->where("d.id_remitente",$rfiltrar);
        }
        if (isset($sFiltrar)&&$sFiltrar!=null&&$sFiltrar!="null") {
            $builder->where("d.id_asunto",$sFiltrar);
        }
        if (isset($dFiltrar)&&$dFiltrar!=null&&$dFiltrar!="null") {
            $builder->where("d.id_destinatario",$dFiltrar);
        }
        return $builder->get()->getResultArray();
    }
}