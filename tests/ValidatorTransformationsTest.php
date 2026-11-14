<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\ConvertCase;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\LTrim;
use PhpSPA\Validator\Attributes\NormalizeCase;
use PhpSPA\Validator\Attributes\RTrim;
use PhpSPA\Validator\Attributes\Trim;
use PhpSPA\Validator\Attributes\Uppercase;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

#[Validatable]
final class TransformationsDto
{
   #[Trim]
   public string $trimmed;

   #[Trim(side: Trim::LEFT)]
   public string $trimLeft;

   #[Trim(side: Trim::RIGHT)]
   public string $trimRight;

   #[Trim('/')]
   public string $trimmedSlashes;

   #[LTrim]
   public string $ltrimmed;

   #[LTrim('#')]
   public string $ltrimmedHash;

   #[RTrim]
   public string $rtrimmed;

   #[RTrim('.')]
   public string $rtrimmedDot;

   #[ConvertCase(ConvertCase::LOWER)]
   public string $lowercase;

   #[ConvertCase(ConvertCase::UPPER)]
   public string $uppercase;

   #[ConvertCase(ConvertCase::UCWORDS)]
   public string $titlecase;

   #[NormalizeCase(NormalizeCase::UCWORDS)]
   public string $normalizedTitle;

   #[Trim]
   #[ConvertCase(ConvertCase::LOWER)]
   #[Email]
   public string $email;

   #[ConvertCase(ConvertCase::UPPER)]
   #[Uppercase]
   public string $validatedUppercase;
}

final class ValidatorTransformationsTest extends TestCase
{
   public function testStringValuesAreTransformedBeforeValidation(): void
   {
      $result = Validator::from([
         'trimmed' => '  value  ',
         'trimLeft' => '  left  ',
         'trimRight' => '  right  ',
         'trimmedSlashes' => '///path/to///',
         'ltrimmed' => '   left only   ',
         'ltrimmedHash' => '###tag###',
         'rtrimmed' => '   right only   ',
         'rtrimmedDot' => 'domain.com...',
         'lowercase' => 'LOWER ME',
         'uppercase' => 'upper me',
         'titlecase' => 'john doe',
         'normalizedTitle' => 'jane doe',
         'email' => '  USER@EXAMPLE.COM  ',
         'validatedUppercase' => 'make me uppercase',
      ], TransformationsDto::class);

      $this->assertTrue($result->isValid());
      $this->assertInstanceOf(TransformationsDto::class, $result->data());

      /** @var TransformationsDto $dto */
      $dto = $result->data();
      $this->assertSame('value', $dto->trimmed);
      $this->assertSame('left  ', $dto->trimLeft);
      $this->assertSame('  right', $dto->trimRight);
      $this->assertSame('path/to', $dto->trimmedSlashes);
      $this->assertSame('left only   ', $dto->ltrimmed);
      $this->assertSame('tag###', $dto->ltrimmedHash);
      $this->assertSame('   right only', $dto->rtrimmed);
      $this->assertSame('domain.com', $dto->rtrimmedDot);
      $this->assertSame('lower me', $dto->lowercase);
      $this->assertSame('UPPER ME', $dto->uppercase);
      $this->assertSame('John Doe', $dto->titlecase);
      $this->assertSame('Jane Doe', $dto->normalizedTitle);
      $this->assertSame('user@example.com', $dto->email);
      $this->assertSame('MAKE ME UPPERCASE', $dto->validatedUppercase);
   }

   public function testTrimRejectsInvalidSide(): void
   {
      $this->expectException(InvalidArgumentException::class);
      $this->expectExceptionMessage('Trim side must be "both", "left", or "right".');

      new Trim(side: 'center');
   }

   public function testConvertCaseRejectsAnUnsupportedCase(): void
   {
      $this->expectException(InvalidArgumentException::class);
      $this->expectExceptionMessage('ConvertCase must be either "lower", "upper", or "ucwords".');

      new ConvertCase('invalid');
   }
}
