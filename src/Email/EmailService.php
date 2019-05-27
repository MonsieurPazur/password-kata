<?php

/**
 * Service in charge of sending various emails.
 */

namespace App\Email;

/**
 * Class EmailService
 *
 * @package App\Email
 */
class EmailService
{
    public const EVENT_RESET_EMAIL = 'reset_email';

    /**
     * Sends email with given message to certain email address.
     *
     * @param string $whereTo email address of the receiver
     * @param string $event what kind of email
     * @param array $args additional arguments
     */
    public function send(string $whereTo, string $event, array $args): void
    {
    }
}
