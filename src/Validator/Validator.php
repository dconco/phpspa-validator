<?php

declare(strict_types=1);

namespace PhpSPA\Validator;

use ReflectionClass;
use PhpSPA\Validator\Attributes\Alpha;
use PhpSPA\Validator\Attributes\AlphaNum;
use PhpSPA\Validator\Attributes\Between;
use PhpSPA\Validator\Attributes\Boolean;
use PhpSPA\Validator\Attributes\Date;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\Enum;
use PhpSPA\Validator\Attributes\Ip;
use PhpSPA\Validator\Attributes\IsArray;
use PhpSPA\Validator\Attributes\Json;
use PhpSPA\Validator\Attributes\Length;
use PhpSPA\Validator\Attributes\Lowercase;
use PhpSPA\Validator\Attributes\Max;
use PhpSPA\Validator\Attributes\MaxItems;
use PhpSPA\Validator\Attributes\MaxLength;
use PhpSPA\Validator\Attributes\Message;
use PhpSPA\Validator\Attributes\Min;
use PhpSPA\Validator\Attributes\MinItems;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Numeric;
use PhpSPA\Validator\Attributes\Phone;
use PhpSPA\Validator\Attributes\Regex;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Attributes\RequiredIf;
use PhpSPA\Validator\Attributes\Timestamp;
use PhpSPA\Validator\Attributes\Uppercase;
use PhpSPA\Validator\Attributes\Url;
use PhpSPA\Validator\Attributes\Uuid;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Attributes\ValidatableType;

final class Validator
{
   /**
    * @param array<string, mixed>|object|null $payload
    * @param class-string<object>|object $class
    */
   public static function from(array|object|null $payload, object|string $class): ValidationResult
   {
      $data = $payload === null ? [] : (\is_object($payload) ? get_object_vars($payload) : $payload);

      $reflection = new ReflectionClass($class);
      if ($reflection->getAttributes(Validatable::class) === []) {
         throw new \InvalidArgumentException('Class must be marked with #[Validatable] to be validated.');
      }
      $dto = $reflection->newInstanceWithoutConstructor();

      $message = self::resolveMessage($reflection);
      $errors = [];

      foreach ($reflection->getProperties() as $property) {
         if (!$property->isPublic()) continue;

         $name = $property->getName();
         $value = $data[$name] ?? null;

         $attributes = $property->getAttributes();

         if (self::isEmpty($value)) {
            $requiredError = self::resolveRequiredError($attributes, $data, $name, $property);
            if ($requiredError !== null) {
               $errors[$name][] = $requiredError;
               continue;
            }

            // No value and not required; skip other validations
            continue;
         }

         foreach ($attributes as $attr) {
            $attrInstance = $attr->newInstance();

            if ($attrInstance instanceof Required || $attrInstance instanceof RequiredIf) {
               continue;
            }

            if ($attrInstance instanceof Email) {
               if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof MinLength) {
               $length = self::stringLength($value);
               if ($length < $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof MaxLength) {
               $length = self::stringLength($value);
               if ($length > $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Length) {
               $length = self::stringLength($value);
               if ($length < $attrInstance->min || $length > $attrInstance->max) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Min) {
               if (!\is_numeric($value) || $value < $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Max) {
               if (!\is_numeric($value) || $value > $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Between) {
               if (!\is_numeric($value) || $value < $attrInstance->min || $value > $attrInstance->max) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Regex) {
               if (!\is_string($value) || preg_match($attrInstance->pattern, $value) !== 1) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Url) {
               if (!filter_var($value, FILTER_VALIDATE_URL)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Uuid) {
               if (!\is_string($value) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Enum) {
               if (!\in_array($value, $attrInstance->values, true)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Boolean) {
               if (!self::isBooleanLike($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Numeric) {
               if (!\is_numeric($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Date) {
               if (!self::isValidDate($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof IsArray) {
               if (!\is_array($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof MinItems) {
               if (!\is_array($value) || \count($value) < $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof MaxItems) {
               if (!\is_array($value) || \count($value) > $attrInstance->value) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Alpha) {
               if (!\is_string($value) || preg_match('/^[a-zA-Z]+$/', $value) !== 1) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof AlphaNum) {
               if (!\is_string($value) || preg_match('/^[a-zA-Z0-9]+$/', $value) !== 1) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Lowercase) {
               if (!\is_string($value) || $value !== mb_strtolower($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Uppercase) {
               if (!\is_string($value) || $value !== mb_strtoupper($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Ip) {
               if (!filter_var($value, FILTER_VALIDATE_IP)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Phone) {
               if (!\is_string($value) || preg_match('/^\+?[0-9\s\-()]{7,20}$/', $value) !== 1) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Json) {
               if (!self::isValidJson($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof Timestamp) {
               if (!self::isValidTimestamp($value)) {
                  $errors[$name][] = $attrInstance->message;
               }
               continue;
            }

            if ($attrInstance instanceof ValidatableType) {
               $nestedErrors = self::validateNested($value, $attrInstance);
               if ($nestedErrors !== null) {
                  $errors[$name] = $nestedErrors;
               }
               continue;
            }
         }

         if (\array_key_exists($name, $data) && !isset($errors[$name])) {
            $property->setValue($dto, $value);
         }
      }

      return new ValidationResult($message, $errors, $errors === [] ? $dto : null);
   }

   private static function resolveMessage(ReflectionClass $reflection): string
   {
      $attrs = $reflection->getAttributes(Message::class);
      if ($attrs === []) {
         return (new Message())->message;
      }

      $instance = $attrs[0]->newInstance();
      return $instance->message;
   }

   /** @param array<int, \ReflectionAttribute> $attributes */
   private static function hasAttribute(array $attributes, string $class): bool
   {
      foreach ($attributes as $attr) {
         if ($attr->getName() === $class) {
            return true;
         }
      }

      return false;
   }

   /**
    * @param array<int, \ReflectionAttribute> $attributes
    * @param array<string, mixed> $data
    */
   private static function resolveRequiredError(array $attributes, array $data, string $field, \ReflectionProperty $property): ?string
   {
      foreach ($attributes as $attr) {
         $instance = $attr->newInstance();

         if ($instance instanceof Required) {
            return $instance->message;
         }

         if ($instance instanceof RequiredIf) {
            $otherValue = $data[$instance->field] ?? null;
            if ($otherValue == $instance->value) {
               return $instance->message;
            }
         }
      }

      if (!$property->hasDefaultValue()) {
         return 'This field is required.';
      }

      return null;
   }

   private static function isEmpty(mixed $value): bool
   {
      if ($value === null) {
         return true;
      }

      if (\is_string($value) && trim($value) === '') {
         return true;
      }

      return false;
   }

   private static function stringLength(mixed $value): int
   {
      $string = (string) $value;
      return function_exists('mb_strlen') ? mb_strlen($string) : \strlen($string);
   }

   private static function isBooleanLike(mixed $value): bool
   {
      if (\is_bool($value)) {
         return true;
      }

      if (\is_int($value) && ($value === 0 || $value === 1)) {
         return true;
      }

      if (\is_string($value)) {
         $normalized = strtolower($value);
         return \in_array($normalized, ['true', 'false', '0', '1'], true);
      }

      return false;
   }

   private static function isValidDate(mixed $value): bool
   {
      if ($value instanceof \DateTimeInterface) {
         return true;
      }

      if (!\is_string($value)) {
         return false;
      }

      return strtotime($value) !== false;
   }

   private static function isValidJson(mixed $value): bool
   {
      if (\is_array($value) || \is_object($value)) {
         return true;
      }

      if (!\is_string($value)) {
         return false;
      }

      try {
         json_decode($value, true, 512, JSON_THROW_ON_ERROR);
         return true;
      } catch (\JsonException) {
         return false;
      }
   }

   private static function isValidTimestamp(mixed $value): bool
   {
      if ($value instanceof \DateTimeInterface) {
         return true;
      }

      if (\is_int($value)) {
         $timestamp = $value;
      } elseif (\is_string($value) && ctype_digit($value)) {
         $timestamp = (int) $value;
      } else {
         return false;
      }

      $dateTime = \DateTimeImmutable::createFromFormat('U', (string) $timestamp);
      return $dateTime !== false;
   }

   private static function validateNested(mixed $value, ValidatableType $attr): ?array
   {
      if ($attr->each) {
         if (!\is_array($value)) {
            return ['message' => $attr->message];
         }

         $nestedErrors = [];
         foreach ($value as $index => $item) {
            if (!\is_array($item) && !\is_object($item)) {
               $nestedErrors[$index] = ['message' => $attr->message];
               continue;
            }

            $result = self::from($item, $attr->class);
            if (!$result->isValid()) {
               $nestedErrors[$index] = $result->errors();
            }
         }

         return $nestedErrors === [] ? null : $nestedErrors;
      }

      if (!\is_array($value) && !\is_object($value)) {
         return ['message' => $attr->message];
      }

      $result = self::from($value, $attr->class);
      return $result->isValid() ? null : $result->errors();
   }
}
