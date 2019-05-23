<?php

/**
 * Class responsible for storing and validating user passwords.
 */

namespace App;

use App\Database\DatabaseInterface;
use App\Email\EmailService;

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
     * @var PasswordGeneratorInterface $passwordGenerator used to generate and verify passwords
     */
    private $passwordGenerator;

    /**
     * @var TokenGeneratorInterface $tokenGenerator used to generate random tokens
     */
    private $tokenGenerator;

    /**
     * @var EmailService $emailService used to send emails
     */
    private $emailService;

    /**
     * PasswordManager constructor.
     *
     * @param PasswordGeneratorInterface $passwordGenerator
     * @param TokenGeneratorInterface $tokenGenerator
     * @param DatabaseInterface $database
     * @param EmailService $emailService
     */
    public function __construct(
        PasswordGeneratorInterface $passwordGenerator,
        TokenGeneratorInterface $tokenGenerator,
        DatabaseInterface $database,
        EmailService $emailService
    ) {
        $this->passwordGenerator = $passwordGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->database = $database;
        $this->emailService = $emailService;
    }

    /**
     * Stores user credentials in database.
     *
     * @param string $email user provided email
     * @param string $rawPassword user provided password
     */
    public function addUserCredentials(string $email, string $rawPassword): void
    {
        $hash = $this->passwordGenerator->generate($rawPassword);
        $this->database->insert(
            'users',
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
            'users',
            [
                'email' => $email
            ]
        );
        return $this->passwordGenerator->verify($rawPassword, $user['password']);
    }

    /**
     * Sends email via emailService for resetting password.
     *
     * @param string $email where to
     */
    public function sendResetEmail(string $email)
    {
        $token = $this->tokenGenerator->get();
        $this->emailService->send(
            $email,
            EmailService::EVENT_RESET_EMAIL,
            [
                'token' => $token
            ]
        );
        $this->database->insert(
            'user_validation_links',
            [
                'email' => $email,
                'token' => $token
            ]
        );
    }
}
