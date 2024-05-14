<?php

namespace Sentgine\Db;

use PDO;
use Exception;
use PDOException;

/**
 * Class QueryBuilder
 * 
 * A simple yet powerful query builder for interacting with MySQL databases.
 */
class QueryBuilder
{
    /** @var PDO The PDO instance. */
    protected PDO $pdo;

    /** @var string The query string. */
    protected string $query;

    /** @var array The query parameters. */
    protected array $parameters = [];

    /** @var string The last executed query. */
    protected string $lastQuery;

    /**
     * DatabaseConnection constructor.
     *
     * @param string $database (Optional) The database name. Defaults to 'database1'.
     * @throws Exception If the provided database configuration is invalid.
     */
    public function __construct(string $database = 'database1')
    {
        $this->connectFromConfig($database);
    }

    /**
     * Establishes a database connection based on the provided database name and an optional default configuration file path.
     *
     * @param string $database The name of the database.
     * @param string|null $defaultConfigPath (Optional) The path to the default database configuration file. Defaults to null.
     * @throws Exception If the provided database configuration is invalid or if the default configuration file is not found.
     */
    protected function connectFromConfig(string $database, ?string $defaultConfigPath = null): void
    {
        // Use the default configuration file path if not provided
        // This expects that you're using the Front Controller pattern /public/index.php
        if ($defaultConfigPath === null) {
            $defaultConfigPath = getcwd() . '/../config/database.php';
        }

        // Check if the default configuration file exists
        if (!file_exists($defaultConfigPath)) {
            return;
        }

        // Load the default configuration
        $config = require $defaultConfigPath;

        // Check if the specified database exists in the configuration
        if (!isset($config[$database])) {
            throw new Exception("Invalid database configuration: {$database}");
        }

        // Connect using the configuration for the specified database
        $this->connect($config[$database]);
    }

    /**
     * Sanitize a string by escaping certain characters or converting it to an integer if it contains only numeric characters.
     *
     * @param string $input The input string to sanitize.
     * @return string|int The sanitized string or integer.
     */
    private function sanitizeString(string $input)
    {
        return is_numeric($input) ? $input : $this->pdo->quote($input);
    }

    /**
     * Establishes a database connection using the provided configuration.
     *
     * @param array $config The database configuration array.
     * @return void
     * @throws Exception If the provided database driver is unsupported.
     * @throws PDOException If an error occurs while establishing the connection.
     */
    public function connect(array $config): void
    {
        $driver = $config['driver'] ?? 'mysql';  // Set default driver to mysql

        if (!in_array($driver, ['mysql', 'pgsql', 'sqlite'])) {
            throw new Exception("Unsupported database driver: {$driver}");
        }

        $dsn = "{$driver}:host={$config['host']};dbname={$config['database']}";

        // Add charset with default if not provided
        $charset = $config['charset'] ?? 'utf8';
        $dsn .= ";charset={$charset}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Rethrow the exception with additional information
            throw new PDOException("Failed to connect to the database: {$e->getMessage()}");
        }
    }

    /**
     * Select columns from a table.
     * 
     * @param string $table The table name.
     * @param mixed $columns (Optional) The columns to select. Defaults to '*'.
     * @return $this
     */
    public function select(string $table, $columns = '*'): self
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        $this->query = "SELECT {$columns} FROM {$table}";
        return $this;
    }

    /**
     * Adds a join clause to the query.
     *
     * @param string $type The type of join (e.g., INNER, LEFT, RIGHT).
     * @param string $table The table to join.
     * @param string $condition The join condition.
     * @return $this
     */
    public function join(string $type, string $table, string $condition): self
    {
        $this->query .= " {$type} JOIN {$table} ON {$condition}";
        return $this;
    }

    /**
     * Adds a WHERE clause to the query.
     *
     * @param string $field The field to filter on.
     * @param mixed $value The value to compare against.
     * @param string $operator (Optional) The comparison operator. Defaults to '='.
     * @return $this
     */
    public function where(string $field, $value, string $operator = '='): self
    {
        $value = $this->sanitizeString($value);
        $this->query .= " WHERE {$field} {$operator} {$value}";
        return $this;
    }

    /**
     * Adds an OR WHERE clause to the query.
     *
     * @param string $field The field to filter on.
     * @param mixed $value The value to compare against.
     * @param string $operator (Optional) The comparison operator. Defaults to '='.
     * @return $this
     */
    public function orWhere(string $field, $value, string $operator = '='): self
    {
        $value = $this->sanitizeString($value);
        $this->query .= " OR {$field} {$operator} {$value}";
        return $this;
    }

    /**
     * Adds an AND WHERE clause to the query.
     *
     * @param string $field The field to filter on.
     * @param mixed $value The value to compare against.
     * @param string $operator (Optional) The comparison operator. Defaults to '='.
     * @return $this
     */
    public function andWhere(string $field, $value, string $operator = '='): self
    {
        $value = $this->sanitizeString($value);
        $this->query .= " AND {$field} {$operator} {$value}";
        return $this;
    }

    /**
     * Limits the number of rows returned by the query.
     *
     * @param int $number The maximum number of rows to return.
     * @return $this
     */
    public function limit(int $number): self
    {
        $this->query .= " LIMIT {$number}";
        return $this;
    }

    /**
     * Sets the OFFSET clause for pagination.
     *
     * @param int $number The number of rows to skip.
     * @return $this
     */
    public function offset(int $number): self
    {
        $this->query .= " OFFSET {$number}";
        return $this;
    }

    /**
     * Executes the built query and returns the result set.
     *
     * @return array The result set as an array of objects.
     * @throws PDOException If an error occurs while executing the query.
     */
    public function get(): array
    {
        try {
            $statement = $this->pdo->prepare($this->query);
            $statement->execute($this->parameters);
            $this->lastQuery = $this->query;
            return $statement->fetchAll(PDO::FETCH_CLASS);
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $this->query;
            throw $e;
        }
    }

    /**
     * Executes a raw SQL query.
     *
     * @param string $sql The raw SQL query to execute.
     * @return array The result set as an array of objects.
     * @throws PDOException If an error occurs while executing the query.
     */
    public function raw(string $sql): array
    {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $this->lastQuery = $sql;
            return $statement->fetchAll(PDO::FETCH_CLASS);
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $sql;
            throw $e;
        }
    }

    /**
     * Inserts data into a table.
     *
     * @param string $table The name of the table.
     * @param array $parameters An associative array where keys are column names and values are the data to be inserted.
     * @return int|string The ID of the last inserted row.
     * @throws PDOException If an error occurs while executing the query.
     */
    public function insert(string $table, array $parameters): int|string
    {
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', array_keys($parameters)),
            ':' . implode(', :', array_keys($parameters))
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
            $this->lastQuery = $sql;
            // Return the last inserted ID
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $sql;
            throw $e;
        }
    }

    /**
     * Updates data in a table with multiple conditions.
     *
     * @param string $table The name of the table.
     * @param array $parameters An associative array where keys are column names and values are the new data.
     * @param array $conditions An associative array representing the conditions for the update operation.
     * @param string $logicalOperator (Optional) The logical operator to use between conditions. Defaults to 'AND'.
     * @return void
     * @throws PDOException If an error occurs while executing the query.
     */
    public function update(string $table, array $parameters, array $conditions, string $logicalOperator = 'AND'): void
    {
        $setPart = '';
        foreach ($parameters as $key => $value) {
            $setPart .= "{$key} = :{$key}, ";
        }
        $setPart = rtrim($setPart, ', ');

        $whereClause = '';
        foreach ($conditions as $field => $value) {
            if (strpos($field, '>') !== false || strpos($field, '<') !== false) {
                $operator = '';
                if (strpos($field, '>') !== false) {
                    $operator = '>=';
                } elseif (strpos($field, '<') !== false) {
                    $operator = '<=';
                }
                $field = str_replace(['>=', '<='], '', $field);
                $whereClause .= "{$field} {$operator} :{$field} {$logicalOperator} ";
            } else {
                $whereClause .= "{$field} = :{$field} {$logicalOperator} ";
            }
            $this->parameters[$field] = $value;
        }
        $whereClause = rtrim($whereClause, " {$logicalOperator} ");

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            $setPart,
            $whereClause
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array_merge($parameters, $conditions));
            $this->lastQuery = $sql;
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $sql;
            throw $e;
        }
    }

    /**
     * Deletes data from a table based on conditions.
     *
     * @param string $table The name of the table.
     * @param array $conditions An associative array representing the conditions for the delete operation.
     * @param string $logicalOperator (Optional) The logical operator to use between conditions. Defaults to 'AND'.
     * @return void
     * @throws PDOException If an error occurs while executing the query.
     */
    public function delete(string $table, array $conditions, string $logicalOperator = 'AND'): void
    {
        $whereClause = '';
        foreach ($conditions as $field => $value) {
            // Check if the condition value is an array, indicating a range condition
            if (is_array($value)) {
                $whereClause .= "{$field} >= :{$field}_min AND {$field} <= :{$field}_max {$logicalOperator} ";
                $this->parameters["{$field}_min"] = $value[0]; // Lower bound
                $this->parameters["{$field}_max"] = $value[1]; // Upper bound
            } else {
                // Regular condition
                $whereClause .= "{$field} = :{$field} {$logicalOperator} ";
                $this->parameters[$field] = $value;
            }
        }
        $whereClause = rtrim($whereClause, " {$logicalOperator} ");

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            $whereClause
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($this->parameters);
            $this->lastQuery = $sql;
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $sql;
            throw $e;
        }
    }

    /**
     * Truncate a table, removing all rows.
     *
     * @param string $table The name of the table to truncate.
     * @return void
     * @throws PDOException If an error occurs while executing the query.
     */
    public function truncate(string $table): void
    {
        $sql = "TRUNCATE TABLE {$table}";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $this->lastQuery = $sql;
        } catch (PDOException $e) {
            // If an exception occurs, preserve the last executed query
            $this->lastQuery = $sql;
            throw $e;
        }
    }

    /**
     * Get the last executed query.
     * 
     * @return string The last executed query.
     */
    public function getLastQuery(): string
    {
        return isset($this->lastQuery) ? $this->lastQuery : 'No query executed yet';
    }

    /**
     * Paginates the query results.
     *
     * @param int $perPage The number of items per page.
     * @param int $currentPage (Optional) The current page number. Defaults to 1.
     * @return array An associative array containing the paginated result set and pagination information.
     * @throws PDOException If an error occurs while executing the query.
     */
    public function paginate(int $perPage, int $currentPage = 1): array
    {
        // Clone the current QueryBuilder instance to avoid modifying the original object
        $paginator = clone $this;

        // Calculate the offset based on the current page and items per page
        $offset = ($currentPage - 1) * $perPage;

        // Count the total number of items (without pagination)
        $totalItems = $paginator->countTotalItems();

        // Apply LIMIT and OFFSET for pagination
        $paginator->limit($perPage)->offset($offset);

        // Execute the modified query
        $results = $paginator->get();

        // Calculate total pages
        $totalPages = ceil($totalItems / $perPage);

        // Return paginated results along with pagination information
        return [
            'data' => $results,
            'pagination' => [
                'total_items' => $totalItems,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Counts the total number of items without applying pagination.
     *
     * @return int The total number of items.
     * @throws PDOException If an error occurs while executing the query.
     */
    protected function countTotalItems(): int
    {
        // Clone the current QueryBuilder instance to avoid modifying the original object
        $counter = clone $this;

        // Remove any existing LIMIT and OFFSET clauses
        $counter->query = preg_replace('/\sLIMIT\s+\d+\sOFFSET\s+\d+$/i', '', $counter->query);

        // Modify the query to perform COUNT(*)
        $counter->query = preg_replace('/^\s*SELECT\s+(?:DISTINCT\s+)?(?:.*?)\s+FROM\s+/i', 'SELECT COUNT(*) FROM ', $counter->query);

        // Execute the modified query
        $statement = $counter->pdo->prepare($counter->query);
        $statement->execute($counter->parameters);

        // Fetch the total count
        $totalCount = $statement->fetchColumn();

        return (int) $totalCount;
    }

    /**
     * Orders the results by one or more specified columns with optional sorting directions.
     * 
     * @param array|string $columns The column(s) to order by. Can be either a string for a single column, an array of columns for multiple column sorting, or an associative array where keys are column names and values are the sorting direction ('ASC' or 'DESC').
     * @param string|null $direction (Optional) The default direction of sorting. Accepts 'ASC' for ascending order or 'DESC' for descending order. Defaults to null.
     * @return $this
     */
    public function orderBy($columns, ?string $direction = null): self
    {
        if (is_array($columns)) {
            // Handle associative array format
            if (array_values($columns) !== $columns) {
                $orderByClauses = [];
                foreach ($columns as $column => $dir) {
                    $orderByClauses[] = "{$column} {$dir}";
                }
                $orderByString = implode(', ', $orderByClauses);
            } else {
                // Handle multiple column sorting
                $orderByString = implode(', ', $columns);
            }
        } else {
            // Handle single column sorting
            $orderByString = $columns;
            $direction = $direction ?? 'ASC'; // Default direction if not provided
        }

        if ($direction !== null) {
            $orderByString .= " {$direction}";
        }

        $this->query .= " ORDER BY {$orderByString}";
        return $this;
    }
}
