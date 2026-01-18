<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class ValidatableType
{
   /**
      * @param class-string<object> $class The DTO class to use for nested validation.
      * @param bool $each When true, validate each item in an array against $class.
      *   When false, validate a single nested object against $class.
      * @param string $message Default error message when nested validation fails.
    */
   public function __construct(
      public string $class,
      public bool $each = false,
      public string $message = 'Invalid nested payload.'
   ) {}
}
