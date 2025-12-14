<?php

class Database
{
    private static ?PDO $_instance = null;
    private PDO $_connection;

    private function __construct()
    {
        $config = config('database');
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        
        try {
            $this->_connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$_instance === null) {
            $db = new self();
            self::$_instance = $db->_connection;
        }
        return self::$_instance;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): bool
    {
        try {
            self::query($sql, $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}


