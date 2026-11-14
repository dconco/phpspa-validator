<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class RTrim extends Trim
{
   public function __construct(string $characters = self::DEFAULT_CHARACTERS)
   {
      parent::__construct(characters: $characters, side: self::RIGHT);
   }
}
