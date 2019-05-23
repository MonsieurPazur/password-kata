<?php

/**
 * Interface for generating random tokens.
 */

namespace App;

/**
 * Interface TokenGeneratorInterface
 *
 * @package App
 */
interface TokenGeneratorInterface
{
    /**
     * Gets random generated token.
     *
     * @return string token
     */
    public function get(): string;
}
