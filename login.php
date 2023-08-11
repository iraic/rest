<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization");
require_once "conexion.php";
require_once "jwt.php";
require_once "config.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "POST"){
    $conexion = conexion();
    $sql = "SELECT username,role FROM users WHERE username=:u AND password=:p";
    $inst = $conexion->prepare($sql);
    $inst->bindValue(':u', $_POST['username']);
    $inst->bindValue(':p', sha1($_POST['password']));
    $inst->execute();
    $result = $inst->fetch(PDO::FETCH_ASSOC);
    if($result){
        header("HTTP/1.1 200 Ok");
        $jwt = JWT::create($result, Config::CLAVE, 7200);
        echo json_encode(["login" => "ok", "jwt" => $jwt]);
    }else{
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["error" => "usuario o contraseña incorrectos"]);
    }
}else{
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["error" => "solo se acepta POST"]);
}




