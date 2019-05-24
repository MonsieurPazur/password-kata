<?php

/**
 * Basic test case for password managing using database connection.
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
use Test\Database\TestPostgreSQLDatabase;

/**
 * Class PasswordManagerDatabaseTest
 *
 * @package Test
 */
class PasswordManagerDatabaseTest extends TestCase
{
    /**
     * @var string test database connection credentials
     */
    const DATABASE_DSN = 'host=localhost dbname=test user=michal password=password';

    /**
     * @var DatabaseInterface $database test database that we operate on
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
     * Sets up database connection.
     *
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->database = new TestPostgreSQLDatabase(self::DATABASE_DSN);
        $this->setUpDatabaseFixture();

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

        // All of the above should happen here.
        $this->manager->addUserCredentials('example@example.com', 'PaS5w0RD1');

        // Check if data is actually in database.
        $result = $this->database->select('users', ['email' => 'example@example.com']);
        $expected = [
            'id' => 1,
            'email' => 'example@example.com',
            'password' => 'hashed_and_salted_PaS5w0RD1'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Creates schema for database.
     */
    private function setUpDatabaseFixture()
    {
        $fixture = file_get_contents(__DIR__ . '/Database/fixture.sql');
        $this->database->query($fixture);
    }
}
