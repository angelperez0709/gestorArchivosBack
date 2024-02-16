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

    public function fetchQuery(PDOStatement $result): array
    {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepareQuery(string $type, ?string $table = null, ?array $data = null, array $params, array $fields): array|int
    {
        $dataSql = $data ? implode(", ", $data) : '';
        $types = [
            'select' => "SELECT $dataSql FROM $table",
            'update' => "UPDATE $table SET $dataSql",
            "function" => "SELECT $dataSql"
        ];
        $sql = $types[$type];

        foreach ($fields['joins'] ?? [] as $key => $value) {
            $sql .= " " . $key . " " . $value;
        }

        $sql .= isset($fields['where']) ? " WHERE " . $fields['where'] : '';
        $sql .= isset($fields['order']) ? " ORDER BY " . $fields['order'] : '';
        $sql .= isset($fields['limit']) ? " LIMIT " . $fields['limit'] : '';

        return $this->executeQuery($sql, $params);
    }

    public function executeQuery($sql, $params): array|int
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);

        if (strpos($sql, 'UPDATE') === 0 || strpos($sql, 'DELETE') === 0) {
            return $stmt->rowCount();
        }
        return $this->fetchQuery($stmt);
    }

    public function insert($table, $data): int|bool
    {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->executeQuery($sql, $data);
        return intval($this->lastInsertId());
    }

    public function checkToken(string $token): int
    {
        try {
            $result = $this->prepareQuery("select", "users", ["id"], ["token" => $token], ["where" => "token = :token"]);
            if (count($result) == 1) {
                return intval($result[0]['id']);
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return 0;
        }
    }

    public function updateToken(int $id): array
    {
        $string = sha1(base64_encode(random_bytes(10)));
        $result = $this->prepareQuery("update", "users", ["token = :token"], ["id" => $id, "token" => $string], ["where" => "id = :id"]);
        return [$result, $string];

    }
}