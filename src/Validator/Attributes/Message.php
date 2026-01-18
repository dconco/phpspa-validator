<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Message
{
   public function __construct(
      public string $message = 'Invalid request payload'
   ) {}
}
