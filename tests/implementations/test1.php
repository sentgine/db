<?php

use Sentgine\Db\QueryBuilder;

require_once './functions.php';

$db = new QueryBuilder();
$db->connect([
    'host' => 'localhost',
    'database' => 'test4',
    'username' => 'root',
    'password' => ''
]);

run($db, function (QueryBuilder $db) {
    $db->select('users');
    $db->where('age', '30.5 string');
    $db->get();
    echo queryInfo($db->getLastQuery());
});

run($db, function (QueryBuilder $db) {
    $db->select('users');
    $db->where('age', 30.5);
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    //dump($result);
});

run($db, function (QueryBuilder $db) {

    $db->select('users');
    $db->where('age', "30");
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    //dump($result);
});

run($db, function (QueryBuilder $db) {
    $name = <<<EOD
        Can't go
    EOD;
    $db->select('users');
    $db->where('name', $name);
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    // dump($result);
});

run($db, function (QueryBuilder $db) {
    $name = <<<EOD
        can't "go"
    EOD;
    $db->select('users');
    $db->where('name', $name);
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    // dump($result);
});


// $db->select('users');
// $db->nestWhere("( 
//     (
//        {{nest1}}
//     ) 
//     AND
//     (
//        {{nest2}}
//     )
//     )
// ");

// $db->where('age', '30.5 string', expression: 'nest1');
// $db->orWhere('age', '30.5 string', expression: 'nest2');
// $db->andWhere('age', '30.5 string', 'expression2');

// $db->where(subQuery: function () use ($db) {
//     // put subquery
//     return $db->raw('SELECT 1 FROM dual');
// }, expression: 'expression3');
