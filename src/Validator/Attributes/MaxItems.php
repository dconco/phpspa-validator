<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class MaxItems
{
   public function __construct(
      public int $value,
      public string $message = 'Must contain at most {value} items.'
   ) {
      $this->message = str_replace('{value}', (string) $this->value, $this->message);
   }
}
