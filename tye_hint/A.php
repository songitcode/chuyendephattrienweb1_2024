<?php
include_once('C.php');
class A extends C
{
    public function a1()
    {
        echo 'This is function a1 from class A.<br>';
    }
}

$instance = new A();
$instance->a1();
?>