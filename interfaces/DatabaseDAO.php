<?php
declare(strict_types=1);
interface DatabaseDAO
{

    /**
     * Prepare a prepared statement.
     *
     * @param string $type The type of query to prepare.
     * @param string $table The table to query.
     * @param array|null $data The data to insert or update.
     * @param array $params The parameters to bind to the query.
     * @param array $fields The fields to use in the query.
     * @return array The result of the query.
     */
    public function prepareQuery(string $type, string $table, ?array $data = null, array $params, array $fields): array|int;

    /**
     * Fetch the result of a query.
     *
     * @param PDOStatement $result The result of the query.
     * @return array Fetched query.
     */
    public function fetchQuery(PDOStatement $result): array;

    /**
     * Execute a query.
     *
     * @param string $sql The query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array|int The result of the query.
     */
    public function executeQuery(string $sql, array $params): array|int;


    /**
     * Insert a new row in the database.
     *
     * @param string $table The table to insert the row into.
     * @return int|bool The id of the new row or false if the insert failed.
     */
    public function insert(string $table, $data): int|bool;

    /**
     * Update the token.
     *
     * @param string $sql The query to execute.
     * @param array $params The parameters to bind to the query.
     * @return array The result of the query.
     */
    public function updateToken(int $id): array|int;
}
