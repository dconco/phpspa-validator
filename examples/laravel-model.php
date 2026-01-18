<?php

declare(strict_types=1);

// Laravel example (Eloquent model as validation target)

use Illuminate\Database\Eloquent\Model;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Validatable;
use PhpSPA\Validator\Validator;

#[Validatable]
final class User extends Model
{
   #[Email]
   public ?string $email = null;

   #[MinLength(8)]
   public string $password;
}

// In a controller:
$result = Validator::from($request->all(), User::class);

if (!$result->isValid()) {
   return response()->json([
      'message' => $result->message(),
      'errors' => $result->errors(),
   ], 422);
}
$user = $result->data();
