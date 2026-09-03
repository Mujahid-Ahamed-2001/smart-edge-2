<?php 
//database connection
// Database connection
class Dbh
{
    private $host = "localhost";
    private $user = "u433703379_araa";
    private $pwd = "Anext-Edge2025";
    private $dbName = "u433703379_araa";

    protected function connect()
    {
        try {
            $dsn = "mysql:host=" . $this->host .
                   ";dbname=" . $this->dbName .
                   ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
            ];

            $pdo = new PDO(
                $dsn,
                $this->user,
                $this->pwd,
                $options
            );

            return $pdo;
        } catch (PDOException $e) {
            echo "Connection failed..." . $e->getMessage();
            return null;
        }
    }
}