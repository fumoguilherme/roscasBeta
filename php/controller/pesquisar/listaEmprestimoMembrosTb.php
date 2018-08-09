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

$membro_codigo = $_REQUEST["membro_codigo"];
$grupo_codigo = $_REQUEST["grupo_codigo"];


if($membro_codigo==""){
    $membros = select("membro_roscas","membro_codigo,membro_nome");
}else{
    $membros = select("membro_roscas","membro_codigo,membro_nome","where membro_codigo = '$membro_codigo'");
}


$tamanho = count($membros);

$dados = array();

for($i=0; $i < $tamanho; $i++){

    $membro_id = $membros[$i]["membro_codigo"];

    $membro_grupo = select("membro_grupo","*","WHERE codigo_membro = '$membro_id' AND md5(codigo_grupo) = '$grupo_codigo'");

    if($membro_grupo){
        echo '<tr>
                  <th scope="row">' . $membros[$i]["membro_codigo"] . '</td>
                   <td><a onclick="gotoEmprestimo(this.id)" id="' .$grupo_codigo.'_'.md5($membros[$i]["membro_codigo"]).'_grupo">' . $membros[$i]['membro_nome'] . '</a></td>
             </tr>';

    }

}
