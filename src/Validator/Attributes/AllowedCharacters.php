<?php

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class AllowedCharacters
{
    public string $characters;
    public string $message;

    public function __construct(string $characters, string $message = 'Contains invalid characters.')
    {
        $this->characters = $characters;
        $this->message = $message;
    }
}
