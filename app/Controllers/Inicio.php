<?php

namespace App\Controllers;
use App\Models\mInicio;

class Inicio extends BaseController
{

    public $conexion;
    public function __construct() {
        $this->conexion = new mInicio();
    }

    public function obterAsuntos()
    {
        $aAsuntos = $this->conexion->obterAsuntos();
        return json_encode($aAsuntos);
    }

    public function obterDocumento()
    {
        $aDocumento = $this->conexion->obterDocumento();
        for ($i=0; $i < count($aDocumento) ; $i++) {
            
            $aDocumento[$i]["fecha"]=explode(' ',$aDocumento[$i]["fecha"])[0];

            $fechaActual = date('Y-m-d'); 
            $datetime1 = date_create(date("Y-m-d",strtotime($aDocumento[$i]["fecha"]."+ ".$aDocumento[$i]["dias"]." days")));
            $datetime2 = date_create($fechaActual);
            $contador = date_diff($datetime2, $datetime1);
            $differenceFormat = '%R%a';
            $aDocumento[$i]["diasVencidos"] = $contador->format($differenceFormat);


            // $aDocumento[$i]["diasVencidos"]=date("Y-m-d",strtotime($aDocumento[$i]["fecha"]."+ ".$aDocumento[$i]["dias"]." days"))-date("Y-m-d");
        }
        return json_encode( $aDocumento);
    }

    public function obterMedio()
    {   
        $aMedio = $this->conexion->obterMedio();
        return json_encode( $aMedio);
        
    }

    public function obterEstado()
    {
        $aEstado = $this->conexion->obterEstado();
        return json_encode( $aEstado);
    }

    public function obterRemitenteDestinatario()
    {
        $aRemitenteDestinatario = $this->conexion->obterRemitenteDestinatario();
        return json_encode( $aRemitenteDestinatario);
    }

    public function actualizarFila()
    {   
        $aDatos = $this->request->getPost();

        $nIdDocumento = $aDatos['id_documento'];

        $aDtosActualizar =[
            'n_registro'=> $aDatos['n_registro'],
            'fecha'=> $aDatos['fecha'],
            'fecha_actualizacion'=> date("Y-m-d H:i:s"),
            'observacion'=> $aDatos['observacion'],
            'id_estado'=>$aDatos['id_estado'],
            'id_medio'=>$aDatos['id_medio'],
            'id_asunto'=>$aDatos['id_asunto'],
            'id_remitente'=>$aDatos['id_remitente'],
            'id_destinatario'=>$aDatos['id_destinatario'],
            'dias'=>$aDatos['dias'],
        ];

        $nResult = $this->conexion->actualizarFila($nIdDocumento,$aDtosActualizar);

        if ($nResult==1) {
           return (json_encode(["result"=>"ok"]));
        }else {
           return (json_encode(["result"=>"error"]));
        }
        
    }

    public function eliminarFila(){

        $id_documento = $this->request->getPost('id_documento');
        $nResult = $this->conexion->eliminarFila($id_documento);
        if ($nResult==1) {
            return (json_encode(["result"=>"ok"]));
         }else {
            return (json_encode(["result"=>"error"]));
         }
    }

    public function guardarFila()
    {
        try {
            $aDatos = $this->request->getPost();

        $aDtosActualizar =[
            'n_registro'=>$aDatos['n_registro'],
            'fecha'=> $aDatos['fecha'],
            'fecha_creacion'=> date("Y-m-d H:i:s"),
            'observacion'=> $aDatos['observacion'],
            'id_estado'=>$aDatos['id_estado'],
            'id_medio'=>$aDatos['id_medio'],
            'id_asunto'=>$aDatos['id_asunto'],
            'id_remitente'=>$aDatos['id_remitente'],
            'id_destinatario'=>$aDatos['id_destinatario'],
            'n_registro'=>$aDatos['n_registro'],
            'dias'=>$aDatos['dias'],
        ];
        
        $aResul = $this->conexion->guardarFila($aDtosActualizar);

        if ($aResul==1) {
            return(json_encode(["result"=>"ok"]));
        } else {
            return(json_encode(["result"=>"error"]));
        }
        } catch (\Throwable $th) {
            return ($th);
        }
        
    }

    public function buscador()
    {
        $sbuscar = $this->request->getPost("sbuscar");

        $aDocumento = $this->conexion->buscador($sbuscar);
        $aDocumento = $this->onTratarDatos($aDocumento,-1);
        return json_encode( $aDocumento);
    }

    public function onTratarDatos($aDocumento,$sFiltroVencido)
    {
        $datosDevolver = [];
        for ($i=0; $i < count($aDocumento) ; $i++) {
            $aDocumento[$i]["fecha"]=explode(' ',$aDocumento[$i]["fecha"])[0];
            $fechaActual = date('Y-m-d'); 
            $datetime1 = date_create(date("Y-m-d",strtotime($aDocumento[$i]["fecha"]."+ ".$aDocumento[$i]["dias"]." days")));
            $datetime2 = date_create($fechaActual);
            $contador = date_diff($datetime2, $datetime1);
            $differenceFormat = '%R%a';
            $aDocumento[$i]["diasVencidos"] = $contador->format($differenceFormat);
            if($sFiltroVencido==0){
                if($aDocumento[$i]["diasVencidos"]==0){
                    $datosDevolver[]=$aDocumento[$i];
                }
            }
            else if($sFiltroVencido==1){
                if($aDocumento[$i]["diasVencidos"]<0){
                    $datosDevolver[]=$aDocumento[$i];
                }
            }
            else if($sFiltroVencido==2){
                if($aDocumento[$i]["diasVencidos"]>0){
                    $datosDevolver[]=$aDocumento[$i];
                }
            }
            else if($sFiltroVencido==3){
                if($aDocumento[$i]["diasVencidos"]<=4&&$aDocumento[$i]["diasVencidos"]>0){
                    $datosDevolver[]=$aDocumento[$i];
                }
            }
            else{
                $datosDevolver[]=$aDocumento[$i];
            }
            // $aDocumento[$i]["diasVencidos"]=date("Y-m-d",strtotime($aDocumento[$i]["fecha"]."+ ".$aDocumento[$i]["dias"]." days"))-date("Y-m-d");
        }
        return $datosDevolver;
    }

    public function filtrar()
    {
        $sFiltrar = $this->request->getGet("sFiltrar");
        $rfiltrar = $this->request->getGet("rfiltrar");
        $dFiltrar = $this->request->getGet("dFiltrar");
        $aFiltrar = $this->request->getGet("aFiltrar");
        $aDocumento =$this->conexion->filtrar($sFiltrar,$rfiltrar,$dFiltrar);
        $aDocumento = $this->onTratarDatos($aDocumento,$aFiltrar);
        return json_encode($aDocumento);
    }

    
}