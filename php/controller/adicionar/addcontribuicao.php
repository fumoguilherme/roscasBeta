<?php
/**
 * Created by PhpStorm.
 * User: kleyton.marcos
 * Date: 7/21/2017
 * Time: 12:13
 */

include_once ("../../dao/adicionar.php");
include_once ("../../dao/apagar.php");
include("../../controller/other/chaves.php");
include_once ("../../dao/pesquisa.php");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$token = $_REQUEST["token"];

$membro_codigo = $_REQUEST["membro_codigo"];

$membro_FS = $_REQUEST["membro_FS"];

$membro_AT = $_REQUEST["membro_AT"];

$membro_Poup = $_REQUEST["membro_Poup"];

$membro_presenca = $_REQUEST["membro_presenca"];

$transaccao_data_criacao = date("Y/m/d H:i:s");

$membro_grupo = select("membro_roscas,membro_grupo","*","WHERE membro_codigo = codigo_membro AND 
codigo_membro = '$membro_codigo' AND md5(codigo_grupo) = '$token'");


$grupo_taxaFS = select("grupo_taxa,taxa_roscas","*","WHERE codigo_taxa = 'TC001' AND codigo_taxa = taxa_codigo AND md5(codigo_grupo) = '$token'");

$grupo_taxaAT = select("grupo_taxa,taxa_roscas","*","WHERE codigo_taxa = 'TC002' AND codigo_taxa = taxa_codigo AND md5(codigo_grupo) = '$token'");

$codigo_tecnico = $_SESSION["tecnico_codigo"];

$membro_FSDiv = $grupo_taxaFS[0]["taxa_valor"] - $membro_FS;

$membro_ATDiv = $grupo_taxaAT[0]["taxa_valor"] - $membro_AT;

$chaveAT = 0; $chaveAT = 0;
if ($codigo_tecnico!="") {

        if ($token!="" && $membro_codigo!="") {

            if ($membro_grupo) {
                $transaccao_codigoFS = chave("transaccao","TR","TR","transaccao_codigo");
                $transaccao_codigoAT = chave("transaccao","TR","TR","transaccao_codigo");
                $transaccao_codigoDep = chave("transaccao","TR","TR","transaccao_codigo");


                $addtransaccaoFS = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
                array($transaccao_codigoFS,$membro_FS,$membro_codigo,$membro_grupo[0]["codigo_grupo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");



                if($addtransaccaoFS) {
                    $addtransaccaoAT = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
                        array($transaccao_codigoAT,$membro_AT,$membro_codigo,$membro_grupo[0]["codigo_grupo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");

                    if($addtransaccaoAT) {

                        $addtransaccaoDep = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
                            array($transaccao_codigoDep,$membro_Poup,$membro_codigo,$membro_grupo[0]["codigo_grupo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");

                        if($addtransaccaoDep) {
                            $contribuicao_codigoFS = chave("contribuicao","CTB","CTB","contribuicao_codigo");
                            $contribuicao_codigoAT = chave("contribuicao","CTB","CTB","contribuicao_codigo");
                            $contribuicao_codigoDep = chave("contribuicao","CTB","CTB","contribuicao_codigo");

                            $addcontribuicaoFS = adicionar(array("contribuicao_codigo","contribuicao_presenca","codigo_taxa","codigo_transaccao","contribuicao_data_criacao"),
                                array($contribuicao_codigoFS,$membro_presenca,'TC001', $transaccao_codigoFS, $transaccao_data_criacao), "contribuicao");

                            if($addcontribuicaoFS) {

                                $addcontribuicaoAT = adicionar(array("contribuicao_codigo", "codigo_taxa","codigo_transaccao","contribuicao_data_criacao"),
                                    array($contribuicao_codigoAT,'TC002', $transaccao_codigoAT, $transaccao_data_criacao), "contribuicao");

                                if($addcontribuicaoAT) {

                                    $addcontribuicaoDEP = adicionar(array("contribuicao_codigo", "codigo_taxa","codigo_transaccao","contribuicao_data_criacao"),
                                        array($contribuicao_codigoDep,'TC003', $transaccao_codigoDep, $transaccao_data_criacao), "contribuicao");

                                    if($addcontribuicaoDEP) {

                                        if ($membro_FS < $grupo_taxaFS[0]["taxa_valor"]) {

                                            $transaccao_codigoFSDiv = chave("transaccao","TR","TR","transaccao_codigo");

                                            $addtransaccaoFSDiv = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
                                                array($transaccao_codigoFSDiv,$membro_FSDiv,$membro_codigo,$membro_grupo[0]["codigo_grupo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");

                                            if ($addtransaccaoFSDiv) {

                                                $divida_codigoFS = chave("divida","DIV","DIV","divida_codigo");

                                                $adddividaFS = adicionar(array("divida_codigo","codigo_taxa","codigo_transaccao","divida_data_criacao"),
                                                    array($divida_codigoFS,'TC001',$transaccao_codigoFSDiv,$transaccao_data_criacao), "divida");

                                            }
                                        }
                                        if ($membro_AT < $grupo_taxaAT[0]["taxa_valor"]) {

                                            $transaccao_codigoATDiv = chave("transaccao","TR","TR","transaccao_codigo");

                                            $addtransaccaoATDiv = adicionar(array("transaccao_codigo","transaccao_valor","codigo_membro","codigo_grupo","codigo_tecnico","transaccao_data_criacao"),
                                                array($transaccao_codigoATDiv,$membro_ATDiv,$membro_codigo,$membro_grupo[0]["codigo_grupo"],$codigo_tecnico,$transaccao_data_criacao), "transaccao");

                                            if ($addtransaccaoATDiv) {

                                                $divida_codigoAT = chave("divida", "DIV", "DIV", "divida_codigo");

                                                $adddividaAT = adicionar(array("divida_codigo", "codigo_taxa", "codigo_transaccao", "divida_data_criacao"),
                                                    array($divida_codigoAT, 'TC002', $transaccao_codigoATDiv, $transaccao_data_criacao), "divida");

                                            }

                                        }

                                        if ($addtransaccaoFSDiv  && $addtransaccaoATDiv) {

                                            if ($adddividaFS && $adddividaAT) {

                                                $mensagem = array(
                                                    'estado' => 'sucesso',
                                                    'membro_nome' => $membro_grupo[0]['membro_nome'],
                                                    'membro_Poup' => $membro_Poup,
                                                    'membro_FS' => $membro_FS,
                                                    'membro_AT' => $membro_AT,
                                                    'membro_FSDiv' => $membro_FSDiv,
                                                    'membro_ATDiv' => $membro_ATDiv,
                                                    'contribuicao_data' => $transaccao_data_criacao,
                                                    'tipo' => 'divFSAT'
                                                );
                                                echo json_encode($mensagem);

                                            } else {
                                                $mensagem = array(
                                                    'estado' => 'erro',
                                                    'tipo' => 'div'
                                                );
                                                echo json_encode($mensagem);
                                            }

                                        } else if ($addtransaccaoFSDiv && !$addtransaccaoATDiv) {

                                            if ($adddividaFS && !$adddividaAT) {

                                                $mensagem = array(
                                                    'estado' => 'sucesso',
                                                    'membro_nome' => $membro_grupo[0]['membro_nome'],
                                                    'membro_Poup' => $membro_Poup,
                                                    'membro_FS' => $membro_FS,
                                                    'membro_AT' => $membro_AT,
                                                    'membro_FSDiv' => $membro_FSDiv,
                                                    'membro_ATDiv' => $membro_ATDiv,
                                                    'contribuicao_data' => $transaccao_data_criacao,
                                                    'tipo' => 'divFS'
                                                );
                                                echo json_encode($mensagem);

                                            } else {
                                                $mensagem = array(
                                                    'estado' => 'erro',
                                                    'tipo' => 'div'
                                                );
                                                echo json_encode($mensagem);
                                            }

                                        }else if ($addtransaccaoATDiv && !$addtransaccaoFSDiv) {

                                            if (!$adddividaFS && $adddividaAT) {

                                                $mensagem = array(
                                                    'estado' => 'sucesso',
                                                    'membro_nome' => $membro_grupo[0]['membro_nome'],
                                                    'membro_Poup' => $membro_Poup,
                                                    'membro_FS' => $membro_FS,
                                                    'membro_AT' => $membro_AT,
                                                    'membro_FSDiv' => $membro_FSDiv,
                                                    'membro_ATDiv' => $membro_ATDiv,
                                                    'contribuicao_data' => $transaccao_data_criacao,
                                                    'tipo' => 'divAT'
                                                );
                                                echo json_encode($mensagem);

                                            } else {
                                                $mensagem = array(
                                                    'estado' => 'erro',
                                                    'tipo' => 'div'
                                                );
                                                echo json_encode($mensagem);
                                            }

                                        }else if (!$addtransaccaoATDiv && !$addtransaccaoFSDiv) {

                                            if (!$adddividaAT && !$adddividaFS) {

                                                $mensagem = array(
                                                    'estado' => 'sucesso',
                                                    'membro_nome' => $membro_grupo[0]['membro_nome'],
                                                    'membro_Poup' => $membro_Poup,
                                                    'membro_FS' => $membro_FS,
                                                    'membro_AT' => $membro_AT,
                                                    'membro_FSDiv' => $membro_FSDiv,
                                                    'membro_ATDiv' => $membro_ATDiv,
                                                    'contribuicao_data' => $transaccao_data_criacao,
                                                    'tipo' => 'good'
                                                );

                                                echo json_encode($mensagem);

                                            } else {
                                                $mensagem = array(
                                                    'estado' => 'erro',
                                                    'tipo' => 'div'
                                                );
                                                echo json_encode($mensagem);
                                            }

                                        }else{
                                            $mensagem = array(
                                                'estado' => 'erro',
                                                'tipo' => 'transDiv'
                                            );
                                            echo json_encode($mensagem);
                                        }

                                    }else {
                                        $mensagem = array(
                                            'estado' => 'erro',
                                            'tipo' => 'ContriFS'
                                        );
                                        echo json_encode($mensagem);
                                    }
                                }else {
                                    $mensagem = array(
                                        'estado' => 'erro',
                                        'tipo' => 'ContriFS'
                                    );
                                    echo json_encode($mensagem);
                                }
                            }else {
                                $mensagem = array(
                                    'estado' => 'erro',
                                    'tipo' => 'ContriFS'
                                );
                                echo json_encode($mensagem);
                            }
                        }else {

                            $mensagem = array(
                                'estado' => 'erro',
                                'tipo' => 'transDepsss'
                            );
                            echo json_encode($mensagem);
                        }
                    }else {

                        $mensagem = array(
                            'estado' => 'erro',
                            'tipo' => 'transAT'
                        );
                        echo json_encode($mensagem);
                    }
                }else {
                    $mensagem = array(
                        'estado' => 'erro',
                        'tipo' => 'transFS'
                    );
                    echo json_encode($mensagem);
                }
            }
            else {

                $mensagem = array(
                    'estado' => 'erro',
                    'tipo' => 'naoexistenogrupo'
                );
                echo json_encode($mensagem);
            }

        }else{
            $mensagem = array(
                'estado' => 'erro',
                'tipo'=>'selecaoMembroOuGrupo'

            );
            echo json_encode($mensagem);
        }
}else{
    $mensagem = array(
        'estado'=>'login'
    );
    echo json_encode($mensagem);
}
