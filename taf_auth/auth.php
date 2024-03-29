<?php

use Taf\TafConfig;
use Firebase\JWT\JWT;
use Taf\TafAuth;

try {
    require '../TafConfig.php';
    require './TafAuth.php';
    $taf_auth = new TafAuth();
    $taf_config = new TafConfig();
    $taf_config->allow_cors();

    $params=$_POST;
    if (file_get_contents('php://input')=="") {
        $params=[];
    } else {
        $params=json_decode(file_get_contents('php://input'),true);
    }   
    // var_dump($params);
    // die;
    $reponse["params"]=$params;

    if(count($params)==0){
        $reponse["status"] = false;
        $reponse["erreur"] = "Parameters required";
        echo json_encode($reponse);
        exit;
    }
    $login=addslashes($params["login"]);
    $pwd=addslashes($params["pwd"]);

    $query = "select * from utilisateur u,role r where u.id_role=r.id_role and email_utilisateur='$login' and password_utilisateur=md5('$pwd') and statut_utilisateur=1 ";
    $resultat = $taf_config->get_db()->query($query)->fetch(PDO::FETCH_ASSOC);
    if ($resultat) {
        $reponse["status"] = true;
        $reponse["data"] = $taf_auth->get_token($resultat);
    } else {
        $reponse["status"] = false;
    }

    echo json_encode($reponse);
} catch (\Throwable $th) {
    $reponse["status"] = false;
    $reponse["erreur"] = $th->getMessage();

    echo json_encode($reponse);
}
