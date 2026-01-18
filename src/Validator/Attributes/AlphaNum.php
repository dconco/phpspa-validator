<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class AlphaNum
{
   public function __construct(
      public string $message = 'Must contain only letters and numbers.'
   ) {}
}
