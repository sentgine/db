<?php

use Sentgine\Db\QueryBuilder;

require_once __DIR__ . '/../../src/QueryBuilder.php';

function dump(array|string $data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function queryInfo(string $string)
{
    echo "<br/><strong>Query <span style='color: green;'>(Correct)</span></strong>: {$string}";
}

function run(QueryBuilder $db, Closure $callable)
{
    try {
        $callable();
    } catch (\Throwable $th) {
        echo "<br><br/><strong>This SQL has a syntax error:</strong>";
        echo "<br/>Query (<span style='color:red;'>Error</span>): {$db->getLastQuery()}";
        echo "<br/>";
    }
}

$db = new QueryBuilder();
$db->connect([
    'host' => 'localhost',
    'database' => 'test4',
    'username' => 'root',
    'password' => ''
]);

run($db, function () use ($db) {
    $db->select('users');
    $db->where('age', '30.5 string');
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    //dump($result);
});

run($db, function () use ($db) {
    $db->select('users');
    $db->where('age', 30.5);
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    //dump($result);
});

run($db, function () use ($db) {

    $db->select('users');
    $db->where('age', "30");
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    //dump($result);
});

run($db, function () use ($db) {
    $name = <<<EOD
        Can't go
    EOD;
    $db->select('users');
    $db->where('name', $name);
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    // dump($result);
});

run($db, function () use ($db) {
    $name = <<<EOD
        can't "go"
    EOD;
    $db->select('users');
    $db->where('name', $name);
    $result = $db->get();
    echo queryInfo($db->getLastQuery());
    // dump($result);
});
