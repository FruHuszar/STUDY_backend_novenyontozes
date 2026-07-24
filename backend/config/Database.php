<?php

declare(strict_types=1);

final class Database
{
    private ?PDO $connection = null;

    private string $host;
    private string $database;
    private string $username;
    private string $password;
    private string $charset;

    public function __construct()
    {
        $this->host = Env::get('DB_HOST', 'localhost');
        $this->database = Env::get('DB_NAME', 'plantwatering');
        $this->username = Env::get('DB_USER', 'root');
        $this->password = Env::get('DB_PASS', '');
        $this->charset = Env::get('DB_CHARSET', 'utf8mb4');
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connection = $this->connect();
        }

        return $this->connection;
    }

    private function connect(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";

        return new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
