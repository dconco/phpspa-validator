<?php

declare(strict_types=1);

namespace PhpSPA\Validator\Attributes;

use Attribute;
use PhpSPA\Validator\Attributes\Message;

#[Message]
#[Attribute(Attribute::TARGET_CLASS)]
final class Validatable
{
}
