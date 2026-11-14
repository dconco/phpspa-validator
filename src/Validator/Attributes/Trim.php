<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Trim
{
   public const BOTH = 'both';
   public const LEFT = 'left';
   public const RIGHT = 'right';

   public const DEFAULT_CHARACTERS = " \t\n\r\v\0\x0B";

   public function __construct(public string $characters = self::DEFAULT_CHARACTERS, public string $side = self::BOTH) {
      if (!\in_array($this->side, [self::BOTH, self::LEFT, self::RIGHT], true)) {
         throw new InvalidArgumentException(
            sprintf('Trim side must be "%s", "%s", or "%s".', self::BOTH, self::LEFT, self::RIGHT)
         );
      }
   }
}
