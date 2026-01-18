# PhpSPA Validator

Attribute-based request validator for PHP (works for all PHP apps or any library).

![Lines of Code](https://raw.githubusercontent.com/dconco/phpspa-validator/main/badge/loc.svg)

## Install

- composer require phpspa/validator

## Quick usage

```php
<?php

use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

#[Validatable]
final class CreateUserDto
{
   #[Email]
   public ?string $email = null; // Optional field

   #[MinLength(8)]
   public string $password; // Required field
}

$result = Validator::from($req->json(), CreateUserDto::class);

if (!$result->isValid() || $result->data() === null) {
   return $res->validationError($result->errors()); // Return error if request payload isn't valid
}

/** @var CreateUserDto $dto */ // !!! Comment is important for IDE autocompletion
$dto = $result->data();

$email = $dto->email; // Optional field (nullable)
$password = $dto->password;
```

### Payload sources

```php
// PhpSPA Request helpers
Validator::from($req->json(), CreateUserDto::class);
Validator::from($req->urlQuery(), CreateUserDto::class);
Validator::from($req->get(), CreateUserDto::class);
Validator::from($req->post(), CreateUserDto::class);

// Raw PHP or other frameworks
Validator::from($_POST, CreateUserDto::class);
Validator::from(['email' => 'me@example.com'], CreateUserDto::class);

// Laravel Request
Validator::from($request->all(), CreateUserDto::class);
```

## Notes

- Classes must be marked with `#[Validatable]` to be validated.
- Optional fields should be declared nullable (e.g., `?string`).
- Access optional fields with null-safe or null coalescing.
- Fields with a default value are treated as optional; fields without a default value are required.
- Use `#[Required(message: "...")]` when you want a custom required-field message.
- Base error message comes from `#[Message]` (default: "Invalid request payload"), add the attribute to the class itself.
- Payload can come from pure PHP `$_POST`, PhpSPA `Request`, Laravel `Request`, Symfony `Request`, or any array/object.
- DTO property names map to request payload keys (e.g., `$email` validates `email`).

Full documentation coming soon.

By the [PhpSPA framework](https://github.com/dconco/phpspa).
