<?php
declare(strict_types=1);
interface DatabaseDAO {

    /**
     * Prepare a prepared statement.
     *
     * @param string $sql The SQL query.
     * @return array The result of the query.
     */
    public function prepareQuery(string $type,string $table,array $fields ) : array;

    /**
     * Fetch the result of a query.
     *
     * @param PDOStatement $result The result of the query.
     * @return array Fetched query.
     */
    public function fetch(PDOStatement $result) : array;

    /**
     * Insert a new row in the database.
     *
     * @param string $table The table to insert the row into.
     * @return int|bool The id of the new row or false if the insert failed.
     */
    public function insert($table,$data) : int|bool;
}
