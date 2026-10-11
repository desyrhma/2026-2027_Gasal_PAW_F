<?php
//2.1
echo "2.1 <br>";
$fruits = ["avocado", "blueberry", "cherry"];
for ($i = 1; $i <= 5; $i++) {
    $fruits[] = "Buah tambahan $i";
}

echo "Panjang array saat ini : " . count($fruits) . "<br><br>";

$arrlength = count($fruits);
for ($x = 0; $x < $arrlength; $x++) {
    echo $fruits[$x] . "<br>";
}

echo "<br>";

//2.2
echo "2.2 <br>";
$sayur = ["Carrot", "Broccoli", "Spinach"];
$arrlength = count($sayur);
for ($x = 0; $x < $arrlength; $x++) {
    echo $sayur[$x] . "<br>";
}
?>