<?php

/**
 * Test database object.
 */

namespace Test\Database;

use App\Database\DatabaseInterface;

/**
 * Class TestPostgreSQLDatabase
 *
 * @package Test\Database
 */
class TestPostgreSQLDatabase implements DatabaseInterface
{
    /**
     * @var resource $connection test database connection
     */
    private $connection;

    /**
     * TestPostgreSQLDatabase constructor.
     *
     * @param string $dsn data source name
     */
    public function __construct(string $dsn)
    {
        $this->connection = pg_connect($dsn);
    }

    /**
     * Inserts values (array column -> value) into table.
     *
     * @param string $table which table to insert to
     * @param array $values column -> value array
     */
    public function insert(string $table, array $values): void
    {
        pg_insert($this->connection, $table, $values);
    }

    /**
     * Selects record(s) from table.
     *
     * @param string $table whitch table to select from
     * @param array $where conditions to apply to query
     *
     * @return array whole matching record or empty array
     */
    public function select(string $table, array $where): array
    {
        $result = pg_select($this->connection, $table, $where);
        if (1 === count($result)) {
            $result = $result[0];
        }
        return $result;
    }

    /**
     * Runs raw query against database.
     *
     * @param string $query raw query to apply
     */
    public function query(string $query): void
    {
        pg_query($this->connection, $query);
    }
}
