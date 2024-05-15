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
    'database' => 'test',
    'username' => 'root',
    'password' => ''
]);

//$db->select('testtable', array("id","title"));
//$db->nestWhereExpression(' 
//    ( 
//        {{nest1}}    
//        OR 
//         {{nest2}}
//    )
//    AND
//    (
//         {{nest3}}
//    )
//    ');
//
//$db->where('id', 1, expression: 'nest1');
//$titlev = <<<EOD
//can't "go"
//EOD;
//$db->Where('title', $titlev, expression: 'nest2');
//$db->orWhere('title', $titlev, expression: 'nest2');
//$db->Where('title', $titlev, expression: 'nest3');
//$db->andWhere('title', $titlev, expression: 'nest3');
//$db->rawWhere('title = 1');
//$db->rawWhere(' OR title = 2');
//$db->where('age', '30.5', ">");
//$db->andWhere('age', '30.5',"<");
//echo $db->buildSQL();



$db->select('dual', array("1 as col1","\"col2\""));
$db->groupBy("col1");
$db->groupBy("col2");
echo $db->buildSQL();



