<?php

/**
 * Basic test case for password managing.
 */

namespace Test;

use App\Database\DatabaseInterface;
use App\PasswordGeneratorInterface;
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
     * @var MockObject|PasswordGeneratorInterface $generator mock
     */
    private $generator;

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
        $this->generator = $this->getMockBuilder(PasswordGeneratorInterface::class)
            ->setMethods(['generate', 'verify'])
            ->getMock();
        $this->manager = new PasswordManager($this->generator, $this->database);
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
     * Tests storing user (with proper password) in database.
     */
    public function testStoreUserInDatabase()
    {
        $this->generator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo('PaS5w0RD1'))
            ->willReturn('hashed_and_salted_PaS5w0RD1');
        $this->database->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('User'),
                $this->equalTo(
                    [
                        'email' => 'example@example.com',
                        'password' => 'hashed_and_salted_PaS5w0RD1'
                    ]
                )
            );
        $this->manager->addUserCredentials('example@example.com', 'PaS5w0RD1');
    }
}
