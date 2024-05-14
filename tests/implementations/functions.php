<?php

use Sentgine\Db\QueryBuilder;

require_once __DIR__ . '/../../src/QueryBuilder.php';

/**
 * Dumps data to the browser in a readable format.
 *
 * @param array|string $data The data to be dumped.
 * @return void
 */
function dump(array|string $data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * Displays query information with a specific message.
 *
 * @param string $string The query string to be displayed.
 * @return void
 */
function queryInfo(string $string): void
{
    echo "<br/><strong>Query <span style='color: green;'>(Correct)</span></strong>: {$string}";
}

/**
 * Executes a callable with the provided QueryBuilder instance.
 *
 * @param QueryBuilder $db The QueryBuilder instance.
 * @param Closure $callable The callable to be executed.
 * @return void
 */
function run(QueryBuilder $db, Closure $callable): void
{
    try {
        $callable($db);
    } catch (\Throwable $th) {
        echo "<br><br/><strong>This SQL has a syntax error:</strong>";
        echo "<br/>Query (<span style='color:red;'>Error</span>): {$db->getLastQuery()}";
        echo "<br/>";
    }
}
