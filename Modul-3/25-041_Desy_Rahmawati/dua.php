<?php
echo '2.1' . '<br>';
$fruits = array("Avocado", "Blueberry", "Cherry");

$tambahan = array("Apple", "Banana", "Mango", "Orange", "Grape");

for ($i = 0; $i < count($tambahan); $i++) {
    $fruits[] = $tambahan[$i];
}

$arrlength = count($fruits);

echo "Jumlah buah: " . $arrlength . "<br>"."<br>";
echo "Daftar buah:<br>";

for ($x = 0; $x < $arrlength; $x++) {
    echo $fruits[$x] . "<br>";
}


echo "<br>"."<br>";
echo '2.2' . '<br>';
$vegies=["Carrot","Brocoli","Spinach"];

for ($i = 0; $i < count($vegies); $i++) {
    echo $vegies[$i] . "<br>";
}
?>
