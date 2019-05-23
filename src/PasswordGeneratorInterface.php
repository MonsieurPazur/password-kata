<?php

/**
 * Interface for generating secure passwords.
 */

namespace App;

/**
 * Interface PasswordGeneratorInterface
 *
 * @package App
 */
interface PasswordGeneratorInterface
{
    /**
     * Generates secure password.
     *
     * @param string $rawPassword raw password to be hashed
     *
     * @return string generated hashed and salted password
     */
    public function generate(string $rawPassword): string;

    /**
     * Verifies that a password matches a hash.
     *
     * @param string $rawPassword raw password
     * @param string $hash given password hash
     *
     * @return bool true if password matches hash
     */
    public function verify(string $rawPassword, string $hash): bool;
}
