<?php

/**
 * Basic test case for password managing.
 */

namespace Test;

use App\Database\DatabaseInterface;
use App\PasswordManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class PasswordManagerTest
 *
 * @package Test
 */
class PasswordManagerTest extends TestCase
{
    /**
     * @var MockObject|DatabaseInterface $database mock
     */
    private $database;

    /**
     * @var PasswordManager $manager object that we operate on
     */
    private $manager;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->database = $this->getMockBuilder(DatabaseInterface::class)
            ->setMethods(['insert'])
            ->getMock();
        $this->manager = new PasswordManager($this->database);
    }

    /**
     * Tests storing and validating user credentials.
     */
    public function testPasswordValidate()
    {
        $this->manager->addUserCredentials('example@example.com', 'PaS5w0RD1');
        $this->assertTrue($this->manager->areValidUserCredentials('example@example.com', 'PaS5w0RD1'));
    }

    /**
     * Tests storing user in database.
     */
    public function testStoreUserInDatabase()
    {
        $this->database->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('User'),
                $this->equalTo(
                    [
                        'email' => 'example@example.com',
                        'password' => 'PaS5w0RD1'
                    ]
                )
            );
        $this->manager->addUserCredentials('example@example.com', 'PaS5w0RD1');
    }
}
