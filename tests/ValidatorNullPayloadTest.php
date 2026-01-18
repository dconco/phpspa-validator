<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Validator;
use PhpSPA\Validator\Validatable;

final class NullPayloadDto extends Validatable
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
