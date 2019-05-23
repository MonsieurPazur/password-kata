<?php

/**
 * Basic test case for password managing.
 */

namespace Test;

use App\Database\DatabaseInterface;
use App\Email\EmailService;
use App\PasswordGeneratorInterface;
use App\PasswordManager;
use App\TokenGeneratorInterface;
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
     * @var MockObject|PasswordGeneratorInterface $passwordGenerator mock
     */
    private $passwordGenerator;

    /**
     * @var MockObject|TokenGeneratorInterface $tokenGenerator mock
     */
    private $tokenGenerator;

    /**
     * @var MockObject|PasswordManager $manager object that we operate on
     */
    private $manager;

    /**
     * @var MockObject|EmailService $emailService mock
     */
    private $emailService;

    /**
     * Sets up mocks and PasswordManager for future tests.
     *
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->database = $this->getMockBuilder(DatabaseInterface::class)
            ->setMethods(['insert', 'select'])
            ->getMock();
        $this->passwordGenerator = $this->getMockBuilder(PasswordGeneratorInterface::class)
            ->setMethods(['generate', 'verify'])
            ->getMock();
        $this->tokenGenerator = $this->getMockBuilder(TokenGeneratorInterface::class)
            ->setMethods(['get'])
            ->getMock();
        $this->emailService = $this->getMockBuilder(EmailService::class)
            ->setMethods(['send'])
            ->getMock();
        $this->manager = new PasswordManager(
            $this->passwordGenerator,
            $this->tokenGenerator,
            $this->database,
            $this->emailService
        );
    }

    /**
     * Tests storing user (with proper password) in database.
     */
    public function testStoreUserInDatabase()
    {
        // We expect generator to create secure salted password.
        $this->passwordGenerator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo('PaS5w0RD1'))
            ->willReturn('hashed_and_salted_PaS5w0RD1');

        // We also expect to insert those (hashed) data into database.
        $this->database->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('users'),
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
                $this->equalTo('users'),
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
        $this->passwordGenerator->expects($this->once())
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

    /**
     * Tests sending reset emails.
     */
    public function testSendingResetEmail()
    {
        $this->tokenGenerator->expects($this->once())
            ->method('get')
            ->willReturn('random_token');
        $this->emailService->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('example@example.com'),
                $this->equalTo('reset_email'),
                $this->equalTo(['token' => 'random_token'])
            );
        $this->database->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('user_validation_links'),
                $this->equalTo(
                    [
                        'email' => 'example@example.com',
                        'token' => 'random_token'
                    ]
                )
            );
        $this->manager->sendResetEmail('example@example.com');
    }
}
