<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Tests;

use PHPUnit\Framework\TestCase;
use PhpSPA\Validator\Attributes\IsArray;
use PhpSPA\Validator\Attributes\MinItems;
use PhpSPA\Validator\Attributes\MaxItems;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Attributes\ValidatableType;
use PhpSPA\Validator\Validator;

#[Validatable]
final class ArrayDto
{
   #[IsArray]
   public array $tags;

   #[MinItems(2)]
   public array $minItems;

   #[MaxItems(2)]
   public array $maxItems;
}

#[Validatable]
final class AddressDto
{
   #[Required]
   public string $city;
}

#[Validatable]
final class UserDto
{
   #[ValidatableType(AddressDto::class)]
   public array $address;

   #[ValidatableType(AddressDto::class, each: true)]
   public array $addresses;
}

final class ValidatorArrayNestedTest extends TestCase
{
   public function testArrayRulesFail(): void
   {
      $payload = [
         'tags' => 'not-array',
         'minItems' => ['one'],
         'maxItems' => ['one', 'two', 'three'],
      ];

      $result = Validator::from($payload, ArrayDto::class);
      $errors = $result->errors();

      $this->assertFalse($result->isValid());
      $this->assertArrayHasKey('tags', $errors);
      $this->assertArrayHasKey('minItems', $errors);
      $this->assertArrayHasKey('maxItems', $errors);
   }

   public function testArrayRulesPass(): void
   {
      $payload = [
         'tags' => ['a'],
         'minItems' => ['a', 'b'],
         'maxItems' => ['a', 'b'],
      ];

      $result = Validator::from($payload, ArrayDto::class);

      $this->assertTrue($result->isValid());
   }

   public function testNestedValidationSingleObject(): void
   {
      $payload = [
         'address' => ['city' => ''],
         'addresses' => [],
      ];

      $result = Validator::from($payload, UserDto::class);

      $this->assertFalse($result->isValid());
      $this->assertArrayHasKey('address', $result->errors());
      $this->assertArrayHasKey('city', $result->errors()['address']);
   }

   public function testNestedValidationArrayEach(): void
   {
      $payload = [
         'address' => ['city' => 'Lagos'],
         'addresses' => [
            ['city' => ''],
            ['city' => 'Abuja'],
         ],
      ];

      $result = Validator::from($payload, UserDto::class);

      $this->assertFalse($result->isValid());
      $this->assertArrayHasKey(0, $result->errors()['addresses']);
      $this->assertArrayHasKey('city', $result->errors()['addresses'][0]);
   }

   public function testNestedValidationArrayEachPasses(): void
   {
      $payload = [
         'address' => ['city' => 'Lagos'],
         'addresses' => [
            ['city' => 'Lagos'],
            ['city' => 'Abuja'],
         ],
      ];

      $result = Validator::from($payload, UserDto::class);

      $this->assertTrue($result->isValid());
   }
}
