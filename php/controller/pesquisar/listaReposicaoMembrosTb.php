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

$tamanho_membros = count($membros);


for($i=0; $i < $tamanho_membros; $i++){

    $membro_id = $membros[$i]["membro_codigo"];

    $membro_grupo = select("membro_grupo","*","WHERE codigo_membro = '$membro_id' AND md5(codigo_grupo) = '$grupo_codigo'");

    if($membro_grupo){

        $controle_dividas = select("transaccao,divida","transaccao.transaccao_codigo,divida.codigo_transaccao,divida.divida_codigo,
                transaccao.codigo_membro,transaccao.transaccao_valor,transaccao.codigo_grupo,divida.divida_taxa_juro,SUM(transaccao.transaccao_valor+(transaccao.transaccao_valor*divida.divida_taxa_juro)/100) AS 'total_divida'","where transaccao.transaccao_codigo = divida.codigo_transaccao and
             transaccao.codigo_membro = '$membro_id' AND md5(transaccao.codigo_grupo) = '$grupo_codigo'");

        $tamanho_dividas = count($controle_dividas);

        if($controle_dividas) {

            for($j=0; $j < $tamanho_dividas; $j++){

                $codigo_divida = $controle_dividas[0]['total_divida'];

                $controle_reposicoes = select("transaccao,reposicao","transaccao.transaccao_codigo,reposicao.codigo_transaccao,reposicao.codigo_divida,
                transaccao.codigo_membro,transaccao.transaccao_valor,transaccao.codigo_grupo,SUM(transaccao.transaccao_valor) AS 'valor_pago'","WHERE 
                        transaccao.transaccao_codigo = reposicao.codigo_transaccao AND
                        transaccao.codigo_membro = '$membro_id' AND 
                        md5(transaccao.codigo_grupo) = '$grupo_codigo'");

                $valor_pago = $controle_reposicoes[0]['valor_pago'];

                if($valor_pago<$codigo_divida) {
                    echo '<tr>
                         <th scope="row">' . $membros[$i]["membro_codigo"].'</td>
                         <td><a onclick="gotoReposicao(this.id)" id="' .$grupo_codigo.'_'.md5($membros[$i]["membro_codigo"]).'_grupo">' . $membros[$i]['membro_nome'] . '</a></td>
                        </tr>';
                }

            }

        }
    }

}


