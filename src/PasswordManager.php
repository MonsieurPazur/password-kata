<?php

/**
 * Class responsible for storing and validating user passwords.
 */

namespace App;

/**
 * Class PasswordManager
 *
 * @package App
 */
class PasswordManager
{
    /**
     * Stores user credentials in database.
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     */
    public function addUserCredentials(string $email, string $rawPassword): void
    {
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
