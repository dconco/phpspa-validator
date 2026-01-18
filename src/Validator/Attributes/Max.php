<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Max
{
   public function __construct(
      public int|float $value,
      public string $message = 'Must be at most {value}.'
   ) {
      $this->message = str_replace('{value}', (string) $this->value, $this->message);
   }
}
