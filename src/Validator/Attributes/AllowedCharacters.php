<?php

namespace PhpSPA\Validator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class AllowedCharacters
{
    public string $characters;
    public int $limit;
    public string $message;

    public function __construct(string $characters, int $limit = PHP_INT_MAX, string $message = 'Contains invalid characters.')
    {
        $this->characters = $characters;
        $this->limit = $limit;
        $this->message = $message;
    }
}
