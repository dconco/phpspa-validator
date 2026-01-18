<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\Alpha;
use PhpSPA\Validator\Attributes\AlphaNum;
use PhpSPA\Validator\Attributes\Between;
use PhpSPA\Validator\Attributes\Boolean;
use PhpSPA\Validator\Attributes\Date;
use PhpSPA\Validator\Attributes\Enum;
use PhpSPA\Validator\Attributes\Ip;
use PhpSPA\Validator\Attributes\Json;
use PhpSPA\Validator\Attributes\Length;
use PhpSPA\Validator\Attributes\Lowercase;
use PhpSPA\Validator\Attributes\Max;
use PhpSPA\Validator\Attributes\MaxLength;
use PhpSPA\Validator\Attributes\Min;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Numeric;
use PhpSPA\Validator\Attributes\Phone;
use PhpSPA\Validator\Attributes\Regex;
use PhpSPA\Validator\Attributes\Timestamp;
use PhpSPA\Validator\Attributes\Uppercase;
use PhpSPA\Validator\Attributes\Url;
use PhpSPA\Validator\Attributes\Uuid;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

#[Validatable]
final class RulesDto
{
   #[MinLength(3)]
   public string $minLength;

   #[MaxLength(5)]
   public string $maxLength;

   #[Length(2, 4)]
   public string $length;

   #[Min(10)]
   public int $min;

   #[Max(5)]
   public int $max;

   #[Between(1, 3)]
   public int $between;

   #[Regex('/^[A-Z]+$/')]
   public string $regex;

   #[Url]
   public string $url;

   #[Uuid]
   public string $uuid;

   #[Enum(['a', 'b'])]
   public string $enum;

   #[Boolean]
   public mixed $bool;

   #[Numeric]
   public mixed $numeric;

   #[Date]
   public mixed $date;

   #[Timestamp]
   public mixed $timestamp;

   #[Alpha]
   public string $alpha;

   #[AlphaNum]
   public string $alphanum;

   #[Lowercase]
   public string $lowercase;

   #[Uppercase]
   public string $uppercase;

   #[Ip]
   public string $ip;

   #[Phone]
   public string $phone;

   #[Json]
   public mixed $json;
}

final class ValidatorRulesTest extends TestCase
{
   public function testRulesProduceErrors(): void
   {
      $payload = [
         'minLength' => 'a',
         'maxLength' => 'toolong',
         'length' => 'x',
         'min' => 5,
         'max' => 9,
         'between' => 10,
         'regex' => 'abc',
         'url' => 'not-url',
         'uuid' => 'not-uuid',
         'enum' => 'c',
         'bool' => 'yes',
         'numeric' => 'abc',
         'date' => 'not-date',
         'timestamp' => 'not-ts',
         'alpha' => 'abc1',
         'alphanum' => 'abc-1',
         'lowercase' => 'ABC',
         'uppercase' => 'abc',
         'ip' => '999.999.999.999',
         'phone' => '123',
         'json' => 'not-json',
      ];

      $result = Validator::from($payload, RulesDto::class);
      $errors = $result->errors();

      $this->assertFalse($result->isValid());
      $this->assertNull($result->data());
      $this->assertArrayHasKey('minLength', $errors);
      $this->assertArrayHasKey('maxLength', $errors);
      $this->assertArrayHasKey('length', $errors);
      $this->assertArrayHasKey('min', $errors);
      $this->assertArrayHasKey('max', $errors);
      $this->assertArrayHasKey('between', $errors);
      $this->assertArrayHasKey('regex', $errors);
      $this->assertArrayHasKey('url', $errors);
      $this->assertArrayHasKey('uuid', $errors);
      $this->assertArrayHasKey('enum', $errors);
      $this->assertArrayHasKey('bool', $errors);
      $this->assertArrayHasKey('numeric', $errors);
      $this->assertArrayHasKey('date', $errors);
      $this->assertArrayHasKey('timestamp', $errors);
      $this->assertArrayHasKey('alpha', $errors);
      $this->assertArrayHasKey('alphanum', $errors);
      $this->assertArrayHasKey('lowercase', $errors);
      $this->assertArrayHasKey('uppercase', $errors);
      $this->assertArrayHasKey('ip', $errors);
      $this->assertArrayHasKey('phone', $errors);
      $this->assertArrayHasKey('json', $errors);
   }

   public function testRulesPassWithValidValues(): void
   {
      $payload = [
         'minLength' => 'abc',
         'maxLength' => 'short',
         'length' => 'abcd',
         'min' => 10,
         'max' => 5,
         'between' => 2,
         'regex' => 'ABC',
         'url' => 'https://example.com',
         'uuid' => '123e4567-e89b-12d3-a456-426614174000',
         'enum' => 'a',
         'bool' => true,
         'numeric' => 12.5,
         'date' => '2024-01-01',
         'timestamp' => time(),
         'alpha' => 'abc',
         'alphanum' => 'abc123',
         'lowercase' => 'abc',
         'uppercase' => 'ABC',
         'ip' => '127.0.0.1',
         'phone' => '+12345678901',
         'json' => ['a' => 1],
      ];

      $result = Validator::from($payload, RulesDto::class);

      $this->assertTrue($result->isValid());
      $this->assertInstanceOf(RulesDto::class, $result->data());
      /** @var RulesDto $dto */
      $dto = $result->data();
      $this->assertSame('abc', $dto->minLength);
      $this->assertSame('short', $dto->maxLength);
   }
}
