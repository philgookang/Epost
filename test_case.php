<?php

include 'Epost.php';

class TestCase {

    public function searchSimple() {
        $list = Epost::new()->setSearch('연희동')->setLimit(5)->getResult();
        var_dump($list);
    }
};

$tc = new TestCase();
$tc->searchSimple();
