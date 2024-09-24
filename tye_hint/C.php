<?php
include_once('I.php');

class C implements I
{
    public function f()
    {
        echo 'CLASS C f() đã được gọi.<br>';
    }
}

$instance = new C();
$instance->f();
?>