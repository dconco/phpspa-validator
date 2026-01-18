<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

#[Validatable]
final class NullPayloadDto
{
   #[Required]
   public string $email;
}

final class ValidatorNullPayloadTest extends TestCase
{
   public function testNullPayloadFailsRequiredFields(): void
   {
      $result = Validator::from(null, NullPayloadDto::class);

      $this->assertFalse($result->isValid());
      $this->assertNull($result->data());
      $this->assertSame(['This field is required.'], $result->errors()['email']);
   }
}
