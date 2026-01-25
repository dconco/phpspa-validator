<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PhpSPA\Validator\Attributes\AlphaNum;
use PhpSPA\Validator\Attributes\AllowedCharacters;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\Length;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;
use PHPUnit\Framework\TestCase;

#[Validatable]
final class RegisterDto
{
    #[Email]
    public $email;

    #[AlphaNum]
    #[AllowedCharacters('_', 2)] // Allow up to two underscores
    #[Length(3, 20)]
    public $username;

    #[MinLength(8)]
    public $password;
}

final class ValidatorAllowedCharactersTest extends TestCase
{
    public function testValidUsername(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'username' => 'dave_conco',
            'password' => 'password123',
        ];

        $result = Validator::from($payload, RegisterDto::class);

        $this->assertTrue($result->isValid());
        $this->assertSame('dave_conco', $result->data()->username);
    }

    public function testInvalidUsernameWithConsecutiveUnderscores(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'username' => 'dave__conco',
            'password' => 'password123',
        ];

        $result = Validator::from($payload, RegisterDto::class);

        $this->assertTrue($result->isValid());
        $this->assertSame('dave__conco', $result->data()->username);
    }

    public function testInvalidUsernameWithSpecialCharacters(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'username' => 'dave@conco',
            'password' => 'password123',
        ];

        $result = Validator::from($payload, RegisterDto::class);

        $this->assertFalse($result->isValid());
        $this->assertArrayHasKey('username', $result->errors());
        $this->assertSame('Contains invalid characters.', $result->errors()['username'][0]);
    }

    public function testValidUsernameWithUnderscoreAtEnd(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'username' => 'dave_conco_',
            'password' => 'password123',
        ];

        $result = Validator::from($payload, RegisterDto::class);

        $this->assertTrue($result->isValid());
        $this->assertSame('dave_conco_', $result->data()->username);
    }
}