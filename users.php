<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization");
require_once "conexion.php";
require_once "jwt.php";
require_once "config.php";
$encabezados = apache_request_headers();
$token = $encabezados['Authorization']; //"Bearer uefwhjb kjcsdvn"
if(strstr($token,"Bearer")) $token = substr($token,7);
if(JWT::verify($token, Config::CLAVE) != 0){
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["error" => "token invalido"]);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        //Consulta
        $conexion = conexion();
        if (isset($_GET['id'])) {
            //consultar por id
            $sql = "SELECT * FROM users WHERE id=:id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id', $_GET['id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            //consultar todos
            $sql = "SELECT * FROM users";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        //header("HTTP/1.1 200 Ok");
        echo json_encode($result);
        break;
    case 'POST':
        //Crear nuevo
        //comprobar que nos envian todos los datos
        if (
            isset($_REQUEST['name']) && isset($_REQUEST['email']) &&
            isset($_REQUEST['username']) && isset($_REQUEST['password']) &&
            isset($_FILES['avatar']) && isset($_REQUEST['role'])
        ) {
            $file = $_REQUEST['name'].basename($_FILES['avatar']['name']);
            move_uploaded_file($_FILES['avatar']['tmp_name'], "fotos/".$file);

            $conexion = conexion();
            $sql = "INSERT INTO users(name, email, username, password, avatar, role)
            VALUES(:n, :e, :u, :p, :a, :r)";
            //carga el sql en la conexion
            $stmt = $conexion->prepare($sql);
            //remplaza el parametro :n por el valor de $_REQUEST['name']
            $stmt->bindValue(':n', $_REQUEST['name']);
            $stmt->bindValue(':e', $_REQUEST['email']);
            $stmt->bindValue(':u', $_REQUEST['username']);
            $stmt->bindValue(':p', sha1($_REQUEST['password']));
            $stmt->bindValue(':a', $file);
            $stmt->bindValue(':r', $_REQUEST['role']);
            //ejecuta la consulta
            $stmt->execute();
            //obtiene el id del registro insertado
            //if($stmt->rowCount() > 0) se inserto
            $id = $conexion->lastInsertId();
            header("HTTP/1.1 201 Created");
            echo json_encode(["id" => $id]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "faltan datos"]);
        }
        break;
    case 'PUT':
        //Actualizar recurso
        if (
            isset($_REQUEST['id']) &&
            isset($_REQUEST['name']) && isset($_REQUEST['email']) &&
            isset($_REQUEST['username']) && isset($_REQUEST['password']) &&
            isset($_REQUEST['avatar']) && isset($_REQUEST['role'])
        ) {
            $conexion = conexion();
            $sql = "UPDATE users SET name=:n, email=:e, username=:u, 
                password=:p, avatar=:a, role=:r WHERE id=:id";
            //carga el sql en la conexion
            $stmt = $conexion->prepare($sql);
            //remplaza el parametro :n por el valor de $_REQUEST['name']
            $stmt->bindValue(':n', $_REQUEST['name']);
            $stmt->bindValue(':e', $_REQUEST['email']);
            $stmt->bindValue(':u', $_REQUEST['username']);
            $stmt->bindValue(':p', sha1($_REQUEST['password']));
            $stmt->bindValue(':a', $_REQUEST['avatar']);
            $stmt->bindValue(':r', $_REQUEST['role']);
            $stmt->bindValue(':id', $_REQUEST['id']);
            //ejecuta la consulta
            $stmt->execute();
            //if($stmt->rowCount() > 0) se inserto
            echo json_encode(["msg" => "Actualizado"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "faltan datos"]);
        }
        break;
    case 'DELETE':
        //Eliminar
        if (isset($_GET['id'])) {
            //consultar por id
            $conexion = conexion();
            $sql = "DELETE FROM users WHERE id=:id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id', $_GET['id']);
            $stmt->execute();
            echo json_encode(["msg" => "Eliminado"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "falta el id"]);
        }
        break;
    case "OPTIONS":
        //Solo para CORS
        header("HTTP/1.1 200 Ok");
        break;
    default:
        //Error
        header("HTTP/1.1 400 Bad Request");
}
