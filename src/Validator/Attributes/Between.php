<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Between
{
   public function __construct(
      public int|float $min,
      public int|float $max,
      public string $message = 'Must be between {min} and {max}.'
   ) {
      $this->message = str_replace(
         ['{min}', '{max}'],
         [(string) $this->min, (string) $this->max],
         $this->message
      );
   }
}
