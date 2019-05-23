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
     * Sets up database and password generator mocks.
     *
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->database = $this->getMockBuilder(DatabaseInterface::class)
            ->setMethods(['insert', 'select'])
            ->getMock();
        $this->generator = $this->getMockBuilder(PasswordGeneratorInterface::class)
            ->setMethods(['generate', 'verify'])
            ->getMock();
        $this->manager = new PasswordManager($this->generator, $this->database);
    }

    /**
     * Tests storing user (with proper password) in database.
     */
    public function testStoreUserInDatabase()
    {
        // We expect generator to create secure salted password.
        $this->generator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo('PaS5w0RD1'))
            ->willReturn('hashed_and_salted_PaS5w0RD1');

        // We also expect to insert those (hashed) data into database.
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

        // All of the above should happen here.
        $this->manager->addUserCredentials('example@example.com', 'PaS5w0RD1');
    }

    /**
     * Tests getting user from database and validating his password.
     */
    public function testValidateUserFromDatabase()
    {
        // We expect getting user by his email.
        $this->database->expects($this->once())
            ->method('select')
            ->with(
                $this->equalTo('User'),
                $this->equalTo(
                    [
                        'email' => 'example@example.com'
                    ]
                )
            )
            ->willReturn(
                [
                    'email' => 'example@example.com',
                    'password' => 'hashed_and_salted_PaS5w0RD1'
                ]
            );

        // Also his password should be fine.
        $this->generator->expects($this->once())
            ->method('verify')
            ->with(
                $this->equalTo('PaS5w0RD1'),
                $this->equalTo('hashed_and_salted_PaS5w0RD1')
            )
            ->willReturn(true);

        $this->assertTrue($this->manager->areValidUserCredentials(
            'example@example.com',
            'PaS5w0RD1'
        ));
    }
}
