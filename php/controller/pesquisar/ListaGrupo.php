<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 07/25/2018
 * Time: 10:36
 */

date_default_timezone_set("Africa/Maputo");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/pesquisa.php");

$grupo_codigo = "";

if(!isset($_REQUEST["grupo_codigo"])){

    $grupo_roscas = select("grupo_roscas","grupo_codigo,grupo_nome,grupo_data_inicio,grupo_data_fim","where grupo_estado like 'activo'");

}else{

    $grupo_codigo = $_REQUEST["grupo_codigo"];
    $grupo_roscas = select("grupo_roscas","grupo_codigo,grupo_nome,grupo_data_inicio,grupo_data_fim","where md5(grupo_codigo) = '$grupo_codigo' AND grupo_estado like 'activo'");

}


$resumo_DEP = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC003'");

$resumo_FS = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC001'");

$resumo_AT = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC002'");


$tamanho = count($grupo_roscas);

$dados = array();

for($i=0; $i < $tamanho; $i++){

    $grupo_codigo = $grupo_roscas[$i]['grupo_codigo'];

    $membrosTotal_roscas = select("membro_grupo", "count(codigo_grupo) as 'total'","where codigo_grupo = '$grupo_codigo'");

    $membrosPresencasTotal = select("transaccao,contribuicao","COUNT(*) AS 'total'","WHERE transaccao.codigo_grupo = '$grupo_codigo'
     AND transaccao.transaccao_codigo = contribuicao.codigo_transaccao AND contribuicao.contribuicao_presenca LIKE 'Presente'");

    $total = $membrosTotal_roscas[0]['total'];
    $nrPresenca = $membrosPresencasTotal[0]['total'];
    $percentagemPresenca = ($nrPresenca * 100) / $total;

    if($grupo_roscas){

        $d = strtotime($grupo_roscas[$i]['grupo_data_inicio']);
        $n_date = date("Y-m-d",$d);


        $dados[]=  array(
            'grupo_dado'=>$grupo_roscas[$i]['grupo_codigo']." - ".$grupo_roscas[$i]['grupo_nome'],
            'grupo_codigo'=>$grupo_roscas[$i]['grupo_codigo'],
            'grupo_nome'=>$grupo_roscas[$i]['grupo_nome'],
            'grupo_data_inicio'=>date("Y-m-d",strtotime($grupo_roscas[$i]['grupo_data_inicio'])),
            'grupo_data_fim'=>date("Y-m-d",strtotime($grupo_roscas[$i]['grupo_data_fim'])),
            'totalmembro'=>$total,
            'total_fs'=>number_format($resumo_FS[$i]['total'],2,'.',','),
            'total_at'=>number_format($resumo_AT[$i]['total'],2,'.',','),
            'total_dep'=>number_format($resumo_DEP[$i]['total'],2,'.',','),
            'presenca'=>$percentagemPresenca."%"
        );

    }

}

echo json_encode($dados);