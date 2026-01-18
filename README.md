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

if (!$result->isValid()) {
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

## Laravel model example

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use PhpSPA\Validator\Attributes\Boolean;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

// --- YOUR LARAVEL MODEL SCHEMA ---
#[Validatable]
final class User extends Model
{
   #[MinLength(2, message: 'Name must be at least 2 chars')]
   public string $name = 'user';

   #[Boolean]
   public string $isAdmin;
}

// --- YOUR CONTROLLER IMPLEMENTATION ---
final class UserController
{
   public function store(Request $request)
   {
      $result = Validator::from($request->all(), User::class);

      if (!$result->isValid()) {
         return response()->json([
            'message' => $result->message(),
            'errors' => $result->errors(),
         ], 422);
      }

      /** @var User $user */
      $user = $result->data();

      return response()->json([
         'message' => 'Validated',
         'data' => $user,
      ]);
   }
}
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

## Attributes reference

- `#[Validatable]` (class): Marks the class as validatable.
- `#[Message(string $message = 'Invalid request payload')]` (class): Base error message.
- `#[Required(string $message = 'This field is required.')]`: Custom required-field message (required is inferred when no default value exists).
- `#[RequiredIf(string $field, mixed $value, string $message = 'This field is required.')]`: Required when another field equals a value.
- `#[Email(string $message = 'Invalid email address.')]`
- `#[MinLength(int $value, string $message = 'Must be at least {value} characters.')]`
- `#[MaxLength(int $value, string $message = 'Must be at most {value} characters.')]`
- `#[Length(int $min, int $max, string $message = 'Length must be between {min} and {max}.')]`
- `#[Min(int|float $value, string $message = 'Must be at least {value}.')]`
- `#[Max(int|float $value, string $message = 'Must be at most {value}.')]`
- `#[Between(int|float $min, int|float $max, string $message = 'Must be between {min} and {max}.')]`
- `#[Regex(string $pattern, string $message = 'Invalid format.')]`
- `#[Url(string $message = 'Invalid URL.')]`
- `#[Uuid(string $message = 'Invalid UUID.')]`
- `#[Enum(array $values, string $message = 'Value must be one of: {values}.')]`
- `#[Boolean(string $message = 'Must be a boolean.')]`
- `#[Numeric(string $message = 'Must be numeric.')]`
- `#[Date(string $message = 'Invalid date.')]`
- `#[Timestamp(string $message = 'Invalid timestamp.')]`
- `#[Alpha(string $message = 'Must contain only letters.')]`
- `#[AlphaNum(string $message = 'Must contain only letters and numbers.')]`
- `#[Lowercase(string $message = 'Must be lowercase.')]`
- `#[Uppercase(string $message = 'Must be uppercase.')]`
- `#[Ip(string $message = 'Invalid IP address.')]`
- `#[Phone(string $message = 'Invalid phone number.')]`
- `#[Json(string $message = 'Invalid JSON.')]`
- `#[IsArray(string $message = 'Must be an array.')]`
- `#[MinItems(int $value, string $message = 'Must contain at least {value} items.')]`
- `#[MaxItems(int $value, string $message = 'Must contain at most {value} items.')]`
- `#[ValidatableType(class-string<object> $class, bool $each = false, string $message = 'Invalid nested payload.')]`:
   Validate nested objects or arrays of objects.

By the [PhpSPA framework](https://github.com/dconco/phpspa).
