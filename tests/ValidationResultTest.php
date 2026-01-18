<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\ValidationResult;

final class ValidationResultTest extends TestCase
{
   public function testValidResultHasNoErrors(): void
   {
      $dto = new \stdClass();
      $dto->name = 'ok';

      $result = new ValidationResult('All good', [], $dto);

      $this->assertTrue($result->isValid());
      $this->assertSame('All good', $result->message());
      $this->assertSame([], $result->errors());
      $this->assertSame($dto, $result->data());
   }

   public function testInvalidResultHasErrors(): void
   {
      $errors = ['name' => ['This field is required.']];
      $result = new ValidationResult('Invalid request payload', $errors, null);

      $this->assertFalse($result->isValid());
      $this->assertSame('Invalid request payload', $result->message());
      $this->assertSame($errors, $result->errors());
      $this->assertNull($result->data());
   }
}
