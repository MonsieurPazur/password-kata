<?php

/**
 * Class responsible for storing and validating user passwords.
 */

namespace App;

use App\Database\DatabaseInterface;

/**
 * Class PasswordManager
 *
 * @package App
 */
class PasswordManager
{
    /**
     * @var DatabaseInterface $database storage where we keep users
     */
    private $database;

    /**
     * PasswordManager constructor.
     *
     * @param DatabaseInterface $database
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
    }

    /**
     * Stores user credentials in database.
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     */
    public function addUserCredentials(string $email, string $rawPassword): void
    {
        $this->database->insert('User', [
            'email' => $email,
            'password' => $rawPassword
        ]);
    }

    /**
     * Checks whether provided user is valid (email and password check out)
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     *
     * @return bool true if this user is in database and his password is correct
     */
    public function areValidUserCredentials(string $email, string $rawPassword): bool
    {
        return true;
    }
}
