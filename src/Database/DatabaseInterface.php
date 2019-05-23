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
}
