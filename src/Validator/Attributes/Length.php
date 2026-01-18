<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Length
{
   public function __construct(
      public int $min,
      public int $max,
      public string $message = 'Length must be between {min} and {max}.'
   ) {
      $this->message = str_replace(
         ['{min}', '{max}'],
         [(string) $this->min, (string) $this->max],
         $this->message
      );
   }
}
