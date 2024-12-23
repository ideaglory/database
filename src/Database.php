<?php

namespace Ideaglory;

/**
 * PHP MySQL Singleton Class
 * 
 * A lightweight and reusable PHP class to manage database connections using the Singleton pattern.
 * This class ensures only one database connection instance exists during the runtime,
 * providing an efficient and easy-to-use database interface.
 * 
 * Features:
 * - Singleton Design Pattern: Ensures a single database connection instance.
 * - Automatic Parameter Binding: Prevents SQL injection by binding parameters securely.
 * - Fetch Methods: Fetch all rows or a single row.
 * - Transaction Management: Includes beginTransaction, commit, and rollback methods.
 * - Last Insert ID: Retrieve the last inserted record ID.
 * 
 * Author: Ideaglory
 * Repository: https://github.com/ideaglory/database
 */

class Database
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $connection;
    private static $instance = null;

    private function __construct($host, $username, $password, $database, $charset)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;

        // Establish the database connection
        $this->connect();
    }

    public static function getInstance($host, $username, $password, $database, $charset)
    {
        if (self::$instance === null) {
            self::$instance = new self($host, $username, $password, $database, $charset);
        }
        return self::$instance;
    }

    private function connect()
    {
        $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        if (!$this->connection->set_charset($this->charset)) {
            die("Error loading character set {$this->charset}: " . $this->connection->error);
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            die("Failed to prepare statement: " . $this->connection->error);
        }

        // Bind parameters if provided
        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            die("Query execution failed: " . $stmt->error);
        }

        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }

    public function close()
    {
        $this->connection->close();
        self::$instance = null; // Reset the instance when connection is closed
    }

    private function getParamTypes($params)
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob or unknown
            }
        }
        return $types;
    }
}
