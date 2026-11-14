<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ConvertCase
{
   public const LOWER = 'lower';
   public const UPPER = 'upper';
   public const UCWORDS = 'ucwords';

   public function __construct(public string $case) {
      if (!\in_array($case, [self::LOWER, self::UPPER, self::UCWORDS], true)) {
         throw new InvalidArgumentException(
            sprintf('ConvertCase must be either "%s", "%s", or "%s".', self::LOWER, self::UPPER, self::UCWORDS)
         );
      }
   }
}
