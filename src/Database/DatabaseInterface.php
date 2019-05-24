<?php

/**
 * Basic interface for simple database.
 */

namespace App\Database;

/**
 * Interface DatabaseInterface
 *
 * @package App\Database
 */
interface DatabaseInterface
{
    /**
     * Inserts values (array column -> value) into table.
     *
     * @param string $table which table to insert to
     * @param array $values column -> value array
     */
    public function insert(string $table, array $values): void;

    /**
     * Selects record(s) from table.
     *
     * @param string $table whitch table to select from
     * @param array $where conditions to apply to query
     *
     * @return array whole matching record or empty array
     */
    public function select(string $table, array $where): array;

    /**
     * Runs raw query against database.
     *
     * @param string $query raw query to apply
     */
    public function query(string $query): void;
}
