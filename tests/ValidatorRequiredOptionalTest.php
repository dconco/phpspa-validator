<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\Optional;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Attributes\RequiredIf;
use PhpSPA\Validator\Attributes\Message;
use PhpSPA\Validator\Validator;
use PhpSPA\Validator\Validatable;

#[Message('Custom base message')]
final class RequiredOptionalDto extends Validatable
{
   #[Required]
   public ?string $name = null;

   #[RequiredIf('role', 'admin')]
   public ?string $token = null;

   #[Optional]
   #[Email]
   public ?string $email = null;
}

final class ValidatorRequiredOptionalTest extends TestCase
{
   public function testRequiredFieldFailsWhenMissing(): void
   {
      $result = Validator::from(['role' => 'user'], RequiredOptionalDto::class);

      $this->assertFalse($result->isValid());
      $this->assertSame('Custom base message', $result->message());
      $this->assertSame(['This field is required.'], $result->errors()['name']);
   }

   public function testRequiredIfFailsWhenConditionMet(): void
   {
      $result = Validator::from(['name' => 'Dave', 'role' => 'admin'], RequiredOptionalDto::class);

      $this->assertFalse($result->isValid());
      $this->assertSame(['This field is required.'], $result->errors()['token']);
   }

   public function testOptionalSkipsValidationWhenMissing(): void
   {
      $result = Validator::from(['name' => 'Dave', 'role' => 'user'], RequiredOptionalDto::class);

      $this->assertTrue($result->isValid());
      $this->assertNull($result->errors()['email'] ?? null);
   }

   public function testEmailValidationRunsWhenProvided(): void
   {
      $result = Validator::from(['name' => 'Dave', 'role' => 'user', 'email' => 'bad'], RequiredOptionalDto::class);

      $this->assertFalse($result->isValid());
      $this->assertSame(['Invalid email address.'], $result->errors()['email']);
   }
}
