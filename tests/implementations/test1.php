<?php

use Sentgine\Db\QueryBuilder;

require_once __DIR__ . '/../../src/QueryBuilder.php';

function dump(array|string $data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function run(QueryBuilder $db, Closure $callable)
{
    try {
        $callable();
    } catch (\Throwable $th) {
        echo "<br/>This SQL has a syntax error:";
        echo "<br/>Query (<span style='color:red;'>Error</span>): {$db->getLastQuery()}";
        echo "<br/><br/>";
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
    $db->where('age', "30");
    // $db->orWhere('name', 'cant "go"');
    $result = $db->get();
    echo "<br/>Query: {$db->getLastQuery()}";
    dump($result);
});

run($db, function () use ($db) {
    $db->select('users');
    $db->where('name', 30, '<');
    $result = $db->get();
    echo "<br/>Query: {$db->getLastQuery()}";
    dump($result);
});
