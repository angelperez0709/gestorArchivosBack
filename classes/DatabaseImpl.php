<?php
declare(strict_types=1);


require_once($_SERVER['DOCUMENT_ROOT'] . "/api/interfaces/DatabaseDAO.php");

class DatabaseImpl extends PDO implements DatabaseDAO
{

    public function __construct(
        $dsn = 'mysql:host=localhost;dbname=managerfiles',
        $username = 'root',
        $password = '',
    ) {
        try {
            parent::__construct($dsn, $username, $password);
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public function fetch(PDOStatement $result): array
    {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepareQuery(string $type,string $table, array $params): array
    {
        $stmt = $this->prepare($sql);
        foreach ($params as $param => $value) {
            $stmt->bindParam($param, $value);
        }
        $stmt->execute();
        return $this->fetch($stmt);
    }

    public function insert($table, $data) : int|bool
    {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->prepareQuery($sql, $data);
        return $this->lastInsertId();
    }

    public function checkToken(string $token): bool
    {
        $sql = 'SELECT id FROM users WHERE token = :token';
        $result = $this->prepareQuery($sql, [':token' => $token]);
        if (count($result) == 1) {
            return $result[0]['id'];
        } else {
            return false;
        }
    }
}