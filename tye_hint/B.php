<?php
include_once('C.php');
class B extends C
{
    public function b1()
    {
        echo 'This is function b1 from class B.<br>';
    }
}

$instance = new B();
$instance->b1();
?>