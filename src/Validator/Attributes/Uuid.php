<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Uuid
{
   public function __construct(
      public string $message = 'Invalid UUID.'
   ) {}
}
