<?php

$arghes = $_GET['args'];
var_dump($arghes);

for ( $i=0; $i<count($arghes); $i++ ){
    if ( !preg_match('/^\w+$/', $arghes[$i]) )
        echo ("err: ".$arghes[$i]."\n");
}
$v = exec("echo " . implode(" ", $arghes));
echo $v;
?>