<?php
declare(strict_types=1);

include_once('I.php');
include_once('A.php');
include_once('B.php');
include_once('C.php');

class Demo implements I
{
    public function f()
    {
        echo "Phương thức f() đã được gọi từ " . __CLASS__ . "<br>";
    }

    public function typeIReturnA(): A
    {
        echo __FUNCTION__ . "<br>";
        return new A();
    }

    public function typeIReturnB(): B
    {
        echo __FUNCTION__ . "<br>";
        return new B();
    }

    public function typeIReturnC(): C
    {
        echo __FUNCTION__ . "<br>";
        return new C();
    }
}

$demo = new Demo();
$instanceA = $demo->typeIReturnA();
$instanceA->f();

$instanceB = $demo->typeIReturnB();
$instanceB->f();

$instanceC = $demo->typeIReturnC();
$instanceC->f();
?>