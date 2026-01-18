<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class RequiredIf
{
   /**
    * @param string $field The other field name to check in the same payload.
    * @param mixed $value When $field equals this value, the current field becomes required.
    * @param string $message Default error message when the current field is missing.
    */
   public function __construct(
      public string $field,
      public mixed $value,
      public string $message = 'This field is required.'
   ) {}
}
