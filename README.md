# DB by Sentgine

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/sentgine/db.svg)](https://packagist.org/sentgine/db)
[![Total Downloads](https://img.shields.io/packagist/dt/sentgine/db.svg)](https://packagist.org/packages/sentgine/db)

A standalone database query builder wrapper using PDO.

## Features

### Database Connection:
- Establishes a database connection using PDO (PHP Data Objects) to interact with MySQL databases.
- Supports multiple database drivers including MySQL, PostgreSQL, and SQLite.
- Handles database connection errors and exceptions gracefully.

### Query Building:
- Provides fluent interface methods for building SQL queries, such as `select`, `join`, `where`, `orWhere`, `andWhere`, `limit`, and `offset`.
- Supports selecting specific columns or all columns (`*`) from a table.
- Allows joining tables with different types of joins (e.g., INNER JOIN, LEFT JOIN, RIGHT JOIN).
- Enables adding WHERE clauses with various operators (e.g., '=', '>', '<', '>=', '<=').

### Query Execution:
- Executes SQL queries using prepared statements to prevent SQL injection attacks.
- Fetches query results as arrays of objects for easy manipulation.
- Handles PDO exceptions and provides error handling mechanisms.

### Data Manipulation:
- Supports inserting data into tables with `insert` method, providing an associative array of column names and values.
- Allows updating data in tables with `update` method, providing new data as an associative array and conditions as an array of conditions.
- Enables deleting data from tables with `delete` method, providing conditions as an associative array of column names and values.

### Pagination:
- Implements pagination of query results using `paginate` method, allowing specification of the number of items per page and the current page number.
- Calculates pagination information including total items, total pages, and current page.
- Supports seamless integration with existing query builder methods for building paginated queries.

### Additional Utilities:
- Provides a `getLastQuery` method to retrieve the last executed query for debugging purposes.
- Implements a `truncate` method to truncate tables, removing all rows.
- Offers a `raw` method for executing raw SQL queries when needed.


## Requirements
- PHP 8.0 or higher.

## Installation

You can install the package via Composer by running the following command:

```bash
composer require sentgine/db:^1.0
```

## Sample Usage of DB

### Basic Usage

```php
<?php

use Sentgine\Db\QueryBuilder;

// Create a new QueryBuilder instance
$queryBuilder = new QueryBuilder();

// Select all columns from the 'users' table
$queryBuilder->select('users')->get();
```

### Select Specific Columns

```php
// Select specific columns from the 'users' table
$queryBuilder->select('users', ['id', 'name', 'email'])->get();
```

### WHERE Clause
```php
// Select users where 'id' is equal to 1
$queryBuilder->select('users')->where('id', 1)->get();
```

### WHERE Clause with Greater Than or Equal To (>=)
```php
// Select users where 'age' is greater than or equal to 18
$queryBuilder->select('users')->where('age', 18, '>=');

// Equivalent to SQL: SELECT * FROM users WHERE age >= 18
```

### WHERE Clause with Less Than or Equal To (<=)
```php
// Select users where 'points' are less than or equal to 100
$queryBuilder->select('users')->where('points', 100, '<=');

// Equivalent to SQL: SELECT * FROM users WHERE points <= 100
```

### WHERE Clause with Greater Than (>)

```php
// Select users where 'salary' is greater than 50000
$queryBuilder->select('users')->where('salary', 50000, '>');

// Equivalent to SQL: SELECT * FROM users WHERE salary > 50000
```

### WHERE Clause with Less Than (<)
```php
// Select users where 'rating' is less than 4.5
$queryBuilder->select('users')->where('rating', 4.5, '<');

// Equivalent to SQL: SELECT * FROM users WHERE rating < 4.5
```

### AND WHERE Clause
```php
// Select users where 'id' is equal to 1 and 'status' is 'active'
$queryBuilder->select('users')->where('id', 1)->andWhere('status', 'active')->get();
```

### OR WHERE Clause
```php
// Select users where 'id' is equal to 1 or 'status' is 'inactive'
$queryBuilder->select('users')->where('id', 1)->orWhere('status', 'inactive')->get();
```

### LIMIT
```php
// Select only 10 users
$queryBuilder->select('users')->limit(10)->get();
```

### OFFSET
```php
// Select users with pagination, skipping the first 10 records
$queryBuilder->select('users')->limit(10)->offset(10)->get();
```

### JOIN
```php
// Select users along with their corresponding posts
$queryBuilder->select('users')
    ->join('INNER', 'posts', 'users.id = posts.user_id')
    ->get();
```

### INSERT
```php
// Insert a new user into the 'users' table
$queryBuilder->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);
```

### UPDATE
```php
// Update user with id 1
$queryBuilder->update('users', ['name' => 'Jane Doe'], ['id' => 1]);
```

### DELETE
```php
// Delete users with id greater than 10
$queryBuilder->delete('users', ['id' => [10, 20]], '>');
```

### Raw Query
```php
// Execute a raw SQL query
$queryBuilder->raw('SELECT * FROM users WHERE id = 1');
```

### Truncate Table
```php
// Truncate the 'users' table
$queryBuilder->truncate('users');
```

### Paginate
```php
use Sentgine\Db\QueryBuilder;

// Instantiate the QueryBuilder class
$queryBuilder = new QueryBuilder();

// Set the number of items per page
$perPage = 10;

// Get the current page number from the request, default to 1 if not provided
$current_page = $_GET['page'] ?? 1;

// Paginate the query results
$paginationData = $queryBuilder->paginate($perPage, $current_page);

// Retrieve the paginated data and pagination information
$data = $paginationData['data'];
$pagination = $paginationData['pagination'];

// Display the paginated data
foreach ($data as $row) {
    // Output each row of data
    echo $row->id . ' - ' . $row->name . '<br>';
}

// Display pagination links
echo '<br>Pagination:';
for ($page = 1; $page <= $pagination['total_pages']; $page++) {
    // Output pagination links
    echo '<a href="?page=' . $page . '">' . $page . '</a> ';
}
```

## Changelog
Please see the [CHANGELOG](https://github.com/sentgine/db/blob/main/CHANGELOG.md) file for details on what has changed.

## Security
If you discover any security-related issues, please email sentgine@gmail.com instead of using the issue tracker.

## Credits
**DB** is built and maintained by Adrian Navaja. Special gratitude to the esteemed Database Specialist Consultant, Dodie Batoctoy. Connect with Dodie on ([LinkedIn](https://www.linkedin.com/in/dodie-batoctoy-56833865/)) for expert insights.
- Check out some cool tutorials and stuff on [YouTube](https://www.youtube.com/@sentgine)!
- Catch my latest tweets and updates on [Twitter](https://twitter.com/sentgine) (formerly X)!
- Let's connect on a more professional note over on [LinkedIn](https://www.linkedin.com/in/adrian-navaja/)!
- For more information about me and my work, visit my website: [sentgine.com](https://www.sentgine.com/).

## License
The MIT License (MIT). Please see the [LICENSE](https://github.com/sentgine/db/blob/main/LICENSE) file for more information.