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
     * @var PasswordGeneratorInterface $generator used to generate and verify passwords
     */
    private $generator;

    /**
     * PasswordManager constructor.
     *
     * @param PasswordGeneratorInterface $generator
     * @param DatabaseInterface $database
     */
    public function __construct(PasswordGeneratorInterface $generator, DatabaseInterface $database)
    {
        $this->database = $database;
        $this->generator = $generator;
    }

    /**
     * Stores user credentials in database.
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     */
    public function addUserCredentials(string $email, string $rawPassword): void
    {
        $hash = $this->generator->generate($rawPassword);
        $this->database->insert(
            'User',
            [
                'email' => $email,
                'password' => $hash
            ]
        );
    }

    /**
     * Checks whether provided user is valid (email and password check out).
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     *
     * @return bool true if this user is in database and his password is correct
     */
    public function areValidUserCredentials(string $email, string $rawPassword): bool
    {
        $user = $this->database->select(
            'User',
            [
                'email' => $email
            ]
        );
        return $this->generator->verify($rawPassword, $user['password']);
    }
}
