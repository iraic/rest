<h1>Arreglos php</h1>
<?php
$frutas = ["pera", "manzana", "uva"];

print_r($frutas);

$frutas[5] = "fresa";
echo "<br>";
print_r($frutas);

$verduras = array("lechuga", "col");
echo "<br>";
print_r($verduras);

$comida = [3=>["no"=>1, "tipo"=>"fruta", "nombre"=>"pera"],
        5=>["no"=>2, "tipo"=>"verdura", "nombre"=>"col"]];
echo "<br>";
print_r($comida);

echo "<br>".$comida[5]["nombre"];