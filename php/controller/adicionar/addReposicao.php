<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 08/03/2018
 * Time: 14:00
 */

date_default_timezone_set("Africa/Maputo");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/adicionar.php");
include("../../controller/other/chaves.php");


$membro_codigo = $_REQUEST["membro_id"];
$grupo_codigo = $_REQUEST["grupo_id"];
$valor_emprestimo = $_REQUEST["valor_emprestimo"];
$taxa_juro = $_REQUEST["taxa_juro"];
$data_pagamento = $_REQUEST["data_pagamento"];

$codigo_tecnico = $_SESSION["tecnico_codigo"];


//$membro_codigo = "f2d2be412024becc5d17bc58f018f821";
//$grupo_codigo = "35b1a289ea9f419e11c3605b61e74971";
//$valor_emprestimo = "1200";
//$taxa_juro = "10";
//$data_pagamento = "2018-08-25";
//
//$codigo_tecnico = "001";


$transaccao_codigo = chave("transaccao","TR","TR","transaccao_codigo");
$divida_codigo = chave("divida", "DIV", "DIV", "divida_codigo");

$transaccao_data_criacao = date("Y/m/d H:i:s");

$membro = select("membro_roscas","membro_codigo","WHERE md5(membro_codigo) = '$membro_codigo'");
$grupo = select("grupo_roscas","grupo_codigo","WHERE md5(grupo_codigo) = '$grupo_codigo'");


if ($codigo_tecnico!="") {


    $addTransaccaoEMP = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
        array($transaccao_codigo,$valor_emprestimo,$membro[0]["membro_codigo"],$grupo[0]["grupo_codigo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");


        if($addTransaccaoEMP){

            $addDividaEMP = adicionar(array("divida_codigo","codigo_taxa","codigo_transaccao","divida_data_criacao","divida_taxa_juro","divida_data_pagamento"),
                array($divida_codigo,'TC004',$transaccao_codigo,$transaccao_data_criacao,$taxa_juro,$data_pagamento), "divida");

            if($addDividaEMP){

                $mensagem = array(
                    'estado'=>'sucesso'
                );
                echo json_encode($mensagem);

            }else{

                $mensagem = array(
                    'estado'=>'erro',
                    'tipo'=>'divida'
                );
                echo json_encode($mensagem);

            }

        }else{
            $mensagem = array(
                'estado'=>'erro',
                'tipo'=>'transacao'
            );
            echo json_encode($mensagem);
        }

}else{
    $mensagem = array(
        'estado'=>'login',
    );
    echo json_encode($mensagem);
}