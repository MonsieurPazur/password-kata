<?php

/**
 * Basic test case for password managing.
 */

namespace Test;

use App\PasswordManager;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordManagerTest
 *
 * @package Test
 */
class PasswordManagerTest extends TestCase
{
    /**
     * Tests storing and validating user credentials.
     */
    public function testPasswordValidate()
    {
        $manager = new PasswordManager();
        $manager->addUserCredentials('example@example.com', 'PaS5w0RD1');
        $this->assertTrue($manager->areValidUserCredentials('example@example.com', 'PaS5w0RD1'));
    }
}
