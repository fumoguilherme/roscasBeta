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


$grupo_codigo = $_REQUEST["grupo_codigo"];

include_once ("../../dao/pesquisa.php");



$resumo_DEP = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC003' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

$resumo_FS = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC001' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

$resumo_AT = select("transaccao,contribuicao","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = contribuicao.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND contribuicao.codigo_taxa = 'TC002' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

$resumo_DIVAT = select("transaccao,divida","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = divida.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND divida.codigo_taxa = 'TC002' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

$resumo_DIVFS = select("transaccao,divida","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = divida.codigo_transaccao 
     AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND divida.codigo_taxa = 'TC001' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");
$total = $resumo_DEP[0]["total"]+$resumo_FS[0]['total']+$resumo_AT[0]['total'];
$dados =  array(
    'total_dep'=>number_format($resumo_DEP[0]["total"],2,'.',','),
    'total_FS'=>number_format($resumo_FS[0]['total'],2,'.',','),
    'total_AT'=>number_format($resumo_AT[0]['total'],2,'.',','),
    'total_ATFSdep'=>number_format($total,2,'.',','),
    'total_DIVAT'=>number_format($resumo_DIVAT[0]['total'],2,'.',','),
    'total_DIVFS'=>number_format($resumo_DIVFS[0]['total'],2,'.',','),
    'total_DIVFSAT'=>number_format($resumo_DIVAT[0]['total'] + $resumo_DIVFS[0]['total'],2,'.',',')

);

echo json_encode($dados);