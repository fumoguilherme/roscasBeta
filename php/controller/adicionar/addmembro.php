<?php
/**
 * Created by PhpStorm.
 * User: kleyton.marcos
 * Date: 7/21/2017
 * Time: 12:13
 */

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/adicionar.php");
include_once ("../../dao/apagar.php");
include("../../controller/other/chaves.php");


$membro_sexo = $_REQUEST["membro_sexo"];
$membro_nome = $_REQUEST["membro_nome"];
$actividade_codigo = $_REQUEST["membro_actividade"];
$membro_contacto = $_REQUEST["membro_contacto"];
$membro_endereco = $_REQUEST["membro_endereco"];
$membro_herdeiro = $_REQUEST["membro_herdeiro"];
$herdeiro_contacto = $_REQUEST["herdeiro_contacto"];
$membro_grau = $_REQUEST["herdeiro_grau_parent"];
$tipo_codigo_documento = $_REQUEST["tipo_codigo_documento"];
$documento_numero = $_REQUEST["documento_numero"];
$usuario_data_criacao = date("Y/m/d H:i:s");
$m_end_date = date('Y-m-d', strtotime('+233 month', strtotime($usuario_data_criacao)));


//$membro_estado = "activo";

$senha = md5($membro_contacto);

$insp = 0;

$membro = select("m_membro", "m_contacto", "WHERE m_contacto ='$membro_contacto'");

$usuario_codigo = $_SESSION["usuario_codigo"];

if ($usuario_codigo!="") {

    if (!$membro || $membro_contacto=='842018') {

        $adicionarMembro = adicionar(array("m_nome", "m_contacto", "m_genero",
            "m_tipo_documento", "m_numero_document", "m_endereco", "m_profissao",
            "m_herdeiro_nome", "m_herdeiro_contacto","m_herdeiro_grau_parent","m_created_by", "m_modified_by",
            "m_created_on", "m_last_modified_on","m_start_date", "m_end_date"),
            array($membro_nome, $membro_contacto, $membro_sexo,
                $tipo_codigo_documento, $documento_numero, $membro_endereco,$actividade_codigo,
                $membro_herdeiro,$herdeiro_contacto,$membro_grau,$usuario_codigo,$usuario_codigo,
                $usuario_data_criacao,$usuario_data_criacao,$usuario_data_criacao,$m_end_date), "m_membro");

        if ($adicionarMembro) {

            $mensagem = array(

                'estado' => 'sucesso',
                'membro_nome' => $membro_nome,
                'tipo' => 'add'
            );

            echo json_encode($mensagem);


        } else {

            $mensagem = array(

                'estado' => 'erro',
                'tipo' => 'addmembro'
            );

            echo json_encode($mensagem);

        }
    } else {
        $mensagem = array(

            'estado' => 'existe'

        );

        echo json_encode($mensagem);
    }
}else{
        $mensagem = array(

            'estado'=>'login'

        );

        echo json_encode($mensagem);
    }
