<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 07/30/2018
 * Time: 10:19
 */

/**
 * Created by PhpStorm.
 * User: guilherme.fumo
 * Date: 7/29/2018
 * Time: 9:41 AM
 */

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

include_once ("../../dao/pesquisa.php");

$tecnico_roscas = select("tecnico_roscas,supervisor_roscas,zona_roscas","tecnico_codigo,tecnico_nome,zona_bairo,zona_caixa","WHERE supervisor_codigo = codigo_supervisor AND codigo_zona = zona_codigo");

$tamanho = count($tecnico_roscas);

$dados = array();

for($i=0; $i < $tamanho; $i++){

        $dados[]=  array(
            'tecnico'=>$tecnico_roscas[$i]['tecnico_codigo']." - ".$tecnico_roscas[$i]['tecnico_nome'],
            'tecnico_id'=>$tecnico_roscas[$i]['tecnico_codigo'],
            'tecnico_caixa_zona'=>$tecnico_roscas[$i]['zona_caixa']." - ".$tecnico_roscas[$i]['zona_bairo']


        );


}

echo json_encode($dados, true);

