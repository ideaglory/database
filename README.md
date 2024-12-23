# PHP Singleton Database Class

A lightweight and reusable PHP class to manage database connections using the Singleton pattern. This class ensures only one database connection instance exists during the runtime, providing an efficient and easy-to-use database interface.

## Features

- Singleton pattern to prevent multiple connections.
- Parameterized query support to prevent SQL injection.
- Basic transaction management (`beginTransaction`, `commit`, `rollback`).
- Utility methods for fetching data (`fetchAll`, `fetchOne`).
- Supports prepared statements with bound parameters.
- Auto-close connection and reset instance when closed.

---

## Installation

You can install this package via [Composer](https://getcomposer.org/). Run the following command:

```bash
composer require ideaglory/database
```

---

## Usage

### Basic Initialization

The `Database` class uses the Singleton pattern. To initialize it:

```php
use Ideaglory\Database;

$db = Database::getInstance('host', 'username', 'password', 'database', 'charset');
```

### Querying the Database

#### Execute a Query

To execute a query with parameters:

```php
$sql = "INSERT INTO users (name, email) VALUES (?, ?)";
$params = ['John Doe', 'john.doe@example.com'];
$db->query($sql, $params);
```

#### Fetch All Rows

```php
$sql = "SELECT * FROM users WHERE status = ?";
$params = ['active'];
$users = $db->fetchAll($sql, $params);
print_r($users);
```

#### Fetch a Single Row

```php
$sql = "SELECT * FROM users WHERE id = ?";
$params = [1];
$user = $db->fetchOne($sql, $params);
print_r($user);
```

### Transactions

#### Begin a Transaction

```php
$db->beginTransaction();
```

#### Commit a Transaction

```php
$db->commit();
```

#### Rollback a Transaction

```php
$db->rollback();
```

### Close Connection

When the database connection is no longer needed:

```php
$db->close();
```

---

## Use Cases

1. **Efficient Database Management**: Ensures only one instance of the database connection exists during runtime, improving resource utilization.
2. **Simplified Query Execution**: Provides easy-to-use methods for executing queries and fetching results.
3. **Secure Applications**: Prevents SQL injection with parameterized queries.
4. **Transaction Support**: Handles database transactions for complex operations.

---

## Example

Below is a complete example demonstrating various features:

```php
use Ideaglory\Database;

try {
    $db = Database::getInstance('localhost', 'root', '', 'example_db', 'utf8mb4');

    // Insert a new user
    $db->query("INSERT INTO users (name, email) VALUES (?, ?)", ['Alice', 'alice@example.com']);

    // Fetch all active users
    $users = $db->fetchAll("SELECT * FROM users WHERE status = ?", ['active']);
    print_r($users);

    // Start a transaction
    $db->beginTransaction();

    // Update a user
    $db->query("UPDATE users SET email = ? WHERE id = ?", ['alice.new@example.com', 1]);

    // Commit the transaction
    $db->commit();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback(); // Rollback if an error occurs
} finally {
    $db->close(); // Close the connection
}
```

---

## Contributing

Feel free to fork this repository and submit pull requests for improvements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Author

Created by [IdeaGlory](https://ideaglory.com).
