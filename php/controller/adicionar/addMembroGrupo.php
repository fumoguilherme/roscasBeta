<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 07/23/2018
 * Time: 15:09
 */

date_default_timezone_set("Africa/Maputo");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/adicionar.php");
include_once ("../../dao/pesquisa.php");
include_once ("../../dao/actualizar.php");

$membro_codigo = $_REQUEST["membro_ids"];
$grupo_codigo = $_REQUEST["grupo_id"];
$codigo_tecnico = $_SESSION["tecnico_codigo"];
$data_inicio = date_create()->format("Y-m-d H:i:s");

$dados = json_decode($membro_codigo);
$tamanho = count($dados);

$grupo = select("grupo_roscas","grupo_codigo, grupo_nome","WHERE md5(grupo_codigo) = '$grupo_codigo'");


if($grupo) {

    for($i = 0; $i<$tamanho ;$i++) {

        $addMembroGrupo = adicionar(array("codigo_membro", "codigo_grupo", "codigo_tecnico", "membro_data_alocacao"),
            array($dados[$i], $grupo[0]["grupo_codigo"], $codigo_tecnico, $data_inicio), "membro_grupo");

    }

    if($addMembroGrupo){


        $actualizar = atualizar("grupo_estado","activo","grupo_roscas","WHERE md5(grupo_codigo) = '$grupo_codigo'");

        if($actualizar){

            $status = array(
                'estado' => 'sucesso',
                'grupo_id'=> $grupo[0]["grupo_codigo"],
                'grupo_nome'=> $grupo[0]["grupo_nome"],
                'grupo_id_enc'=> md5($grupo[0]["grupo_codigo"])
            );
            echo json_encode($status);

        }else{

            $status = array(
                'estado' => 'erro'
            );

            echo json_encode($status);

        }

    }else{
        $status = array(
            'estado' => 'erro'
        );

        echo json_encode($status);
    }

}else{


    $status = array(
        'estado' => 'erro'
    );

    echo json_encode($status);

}



