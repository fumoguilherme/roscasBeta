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

$membros = select("membro_roscas","membro_codigo,membro_nome");

$tamanho = count($membros);

$dados = array();
$divFS= 0;
$divAT= 0;
for($i=0; $i < $tamanho; $i++){

    $membro_id = $membros[$i]["membro_codigo"];

    $membro_grupo = select("membro_grupo","*","WHERE codigo_membro = '$membro_id' AND md5(codigo_grupo) = '$grupo_codigo'");

    $controle_dividasFS = select("transaccao,divida","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = divida.codigo_transaccao 
    and transaccao.codigo_membro = '$membro_id' AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND divida.codigo_taxa = 'TC001' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

    $controle_dividasAT = select("transaccao,divida","SUM(transaccao_valor) AS 'total'","where transaccao.transaccao_codigo = divida.codigo_transaccao 
    and transaccao.codigo_membro = '$membro_id' AND md5(transaccao.codigo_grupo) = '$grupo_codigo' AND divida.codigo_taxa = 'TC002' 
    AND DAY(transaccao.transaccao_data_criacao) = DAY(NOW())");

    if($controle_dividasFS[0]['total']!=''){
        $divFS = $controle_dividasFS[0]['total'];
    }else{
        $divFS= 0;
    }

    if($controle_dividasAT[0]['total']!=''){
        $divAT = $controle_dividasAT[0]['total'];
    }else{
        $divAT= 0;
    }

    if($membro_grupo){
        if($divAT !=0 || $divFS!=0) {
            echo '<tr>
             <td>' . $membros[$i]['membro_nome'] . '</td>
             <td class="text-danger">' . $divAT . '</td>
             <td class="text-danger">' . $divFS . '</td>
            </tr>';
        }
    }

}
