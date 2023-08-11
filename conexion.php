<?php
function conexion(){
    try{
        //cadena de conexion a mysql, servidor, bd, usuario y contraseña
        $conexion = new PDO("mysql:host=localhost;dbname=forodb","root","");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}