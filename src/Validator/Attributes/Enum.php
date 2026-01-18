<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Enum
{
   /**
    * @param array<int|string|float|bool> $values
    * @param string $message
    */
   public function __construct(
      public array $values,
      public string $message = 'Value must be one of: {values}.'
   ) {
      $this->values = $values;
      $this->message = str_replace('{values}', implode(', ', $values), $this->message);
   }
}
