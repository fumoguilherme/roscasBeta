<?php
/**
 * Created by PhpStorm.
 * User: TechJonas
 * Date: 07/23/2018
 * Time: 2:16 PM
 */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
date_default_timezone_set("Africa/Maputo");

include_once ("../../dao/pesquisa.php");
include_once("../../dao/actualizar.php");

$username = $_REQUEST["username"];
$senha = md5($_REQUEST["password"]);
$tipo_usuario = $_REQUEST["tipo_usuario"];
$data = date_create()->format("Y-m-d H:i:s");


$utilizador = select("u_usuario", "*", "WHERE u_nome LIKE '$username'");


if($utilizador){

$users = select("u_usuario", "*", "WHERE u_nome LIKE '$username' AND u_senha LIKE '$senha'");

    if($users && $users!=null){

        $usuario_codigo = $users[0]["u_id"];
        $staff_codigo = $users[0]["s_id"];

        if(true){

            if(true){

                //Coloque aqui as intrucoes, ele ja foi autorizado

                $s_staff =  select("s_staff", "*", "WHERE s_id = '$staff_codigo'");

                $atualizar = atualizar("u_last_modified_on","$data",
                    "u_usuario", "WHERE u_id LIKE '$usuario_codigo'");

                $_SESSION["usuario_codigo"] = $users[0]["u_id"];
                $_SESSION["usuario_nome"] = $users[0]["u_nome"];
                $_SESSION["tecnico_codigo"] = $s_staff[0]["s_id"];
                $_SESSION["tecnico_nome"] = $s_staff[0]["s_nome"];
                $_SESSION["tecnico_email"] = $s_staff[0]["s_nome"];
                $_SESSION["usuario_contacto"] = $users[0]["u_contacto"];

                $status = array(
                    'estado'=>'sucesso'
                );

                echo json_encode($status);

            }else{

                echo "Não é técnico";

            }

        }else if(strcmp($tipo_usuario,"Admin") == 0){

            $tipo = select("usuario_tipo_roscas", "*", "WHERE codigo_tipo LIKE '1' AND codigo_usuario LIKE '$usuario_codigo'");

            if($tipo && $tipo!=null){


                //Coloque aqui as intrucoes, ele ja foi autorizado


            }else{

                echo "Não é Admin";

            }

        }else if(strcmp($tipo_usuario,"Membro") == 0){

            $tipo = select("usuario_tipo_roscas", "*", "WHERE codigo_tipo LIKE '3' AND codigo_usuario LIKE '$usuario_codigo'");

            if($tipo && $tipo!=null){


                //Coloque aqui as intrucoes, ele ja foi autorizado


            }else{

                echo "Não é Membro";

            }

        }

    }else {
        $status = array(
            'estado'=>'erro'
        );

        echo json_encode($status);
    }
}else{
    $status = array(
        'estado'=>'erro'
    );

    echo json_encode($status);

}

