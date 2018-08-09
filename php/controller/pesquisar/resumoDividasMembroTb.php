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


$codigo_membro = $_REQUEST["membro_codigo"];

$grupo_codigo = $_REQUEST["grupo_codigo"];

$membros = select("membro_roscas","membro_codigo,membro_nome","WHERE md5(membro_codigo) = '$codigo_membro'");

if($membros){

    $membro_id = $membros[0]["membro_codigo"];

    $membro_grupo = select("membro_grupo","*","WHERE codigo_membro = '$membro_id' AND md5(codigo_grupo) = '$grupo_codigo'");
    if($membro_grupo){

        $controle_dividas = select("transaccao,divida","transaccao.transaccao_codigo,divida.codigo_transaccao,divida.divida_codigo,
                transaccao.codigo_membro,transaccao.transaccao_valor,transaccao.codigo_grupo,divida.codigo_taxa,divida.divida_taxa_juro,
                SUM(transaccao.transaccao_valor+(transaccao.transaccao_valor*divida.divida_taxa_juro)/100) AS 'total_divida'",
            "where transaccao.transaccao_codigo = divida.codigo_transaccao and
             transaccao.codigo_membro = '$membro_id' AND md5(transaccao.codigo_grupo) = '$grupo_codigo' group by divida.divida_codigo");

        if($controle_dividas){

            $tamanho = count($controle_dividas);
            echo '<div class="demo-checkbox">';
            for($i=0; $i < $tamanho; $i++){

                $codigo_divida = $controle_dividas[$i]['divida_codigo'];
                $codigo_taxa = $controle_dividas[$i]['codigo_taxa'];
                $taxa_roscas = select("taxa_roscas","*","WHERE taxa_codigo = '$codigo_taxa'");
                $total_divida = $controle_dividas[$i]['total_divida'];


                $controle_reposicoes = select("transaccao,reposicao","transaccao.transaccao_codigo,reposicao.codigo_transaccao,reposicao.codigo_divida,
                transaccao.codigo_membro,transaccao.transaccao_valor,transaccao.codigo_grupo,SUM(transaccao.transaccao_valor) AS 'total_pago'","WHERE 
                        transaccao.transaccao_codigo = reposicao.codigo_transaccao AND
                        reposicao.codigo_divida = '$codigo_divida' AND 
                        transaccao.codigo_membro = '$membro_id' AND 
                        md5(transaccao.codigo_grupo) = '$grupo_codigo'");

                if($controle_reposicoes){

                    $valor_pago = $controle_reposicoes[0]['total'];
                    $descricao = $taxa_roscas[0]['taxa_descricao'];
                    $porpagar =$total_divida-$valor_pago;
                    echo ' <tr> 
                            <th>'.$descricao.'</th>
                            <td class="text-danger" id="'.$codigo_divida.'_valor_td">'.number_format($porpagar,2,'.',',').'</td>
                            <td>
                                <input type="checkbox"  id="'.$codigo_divida.'_checkbox" onclick="somaDivida(this.id)" class="chk-col-light-green" />
                                <label for="'.$codigo_divida.'_checkbox" class="text-success">Pagar</label>
                            </td>
                            </tr>';

                        $dados[] =  array(
                            'membro_dado'=>$membros[0]['membro_codigo']." - ".$membros[0]['membro_nome'],
                            'membro_codigo'=>$membros[0]['membro_codigo'],
                            'membro_nome'=>$membros[0]['membro_nome'],
                            'total_divida'=>$total_divida,
                            'valor_divida'=>$valor_pago,
                            'codigo_divida'=>$codigo_divida


                        );



                }

            }
            echo '</div>';

        }

    }
}
