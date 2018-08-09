<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 08/03/2018
 * Time: 16:34
 */

date_default_timezone_set("Africa/Maputo");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/pesquisa.php");
$membro_codigo = $_REQUEST["membro_id"];
$grupo_codigo = $_REQUEST["grupo_id"];


$resumo_DEP = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND
      md5(transaccao.codigo_membro) = '$membro_codigo' AND contribuicao.codigo_taxa = 'TC003'");

$membro_nome = select("membro_roscas","membro_nome,membro_codigo","WHERE md5(membro_codigo) = '$membro_codigo'");

$taxa_grupo = select("grupo_roscas","grupo_taxa_juros","WHERE md5(grupo_codigo) = '$grupo_codigo'");

if($resumo_DEP){

    if($membro_nome){

        if($taxa_grupo){

            $mensagem = array(
                'estado' => 'sucesso',
                'membro_nome' =>$membro_nome[0]['membro_nome'],
                'membro_capacidade' =>number_format(($resumo_DEP[0]["total"] * 2),2,'.',','),
                'grupo_taxa' => $taxa_grupo[0]["grupo_taxa_juros"],
                'capacidade_nao_formatado' => ($resumo_DEP[0]["total"] * 2),

            );
            echo json_encode($mensagem);

        }else{

            $mensagem = array(
                'estado' => 'erro',
            );
            echo json_encode($mensagem);

        }

    }else{

        $mensagem = array(
            'estado' => 'erro',
        );
        echo json_encode($mensagem);

    }

}else{

    $mensagem = array(
        'estado' => 'erro',
    );
    echo json_encode($mensagem);

}
