<?php

declare(strict_types=1);

use PhpSPA\App;
use PhpSPA\Http\Request;
use PhpSPA\Http\Response;
use PhpSPA\Http\Router;
use PhpSPA\Validator\Attributes\Email;
use PhpSPA\Validator\Attributes\MinLength;
use PhpSPA\Validator\Attributes\Optional;
use PhpSPA\Validator\Attributes\Required;
use PhpSPA\Validator\Validatable;
use PhpSPA\Validator\Validator;

require_once '../vendor/autoload.php';

final class CreateUserDto extends Validatable
{
   #[Required]
   #[Email]
   public string $email;

   #[Required]
   #[MinLength(8)]
   public string $password;

   // Optional field with custom per-field message
   #[Optional]
   #[MinLength(2, message: 'Name must be at least 2 chars')]
   public string $name;
}

$app = new App();

$app->prefix('/api', function (Router $router): void {
   // Validator middleware example
   $router->middleware(function (Request $req, Response $res, Closure $next) {
      // Validate JSON payload into DTO
      $result = Validator::from($req->json(), CreateUserDto::class);

      if (!$result->isValid() || $result->data() === null) {
         // Default messages are returned when not provided.
         return $res->validationError($result->errors());
      }

      // Pass the DTO forward (optional convenience)
      $req->validated = $result->data();

      return $next();
   });

   $router->post('/users', function (Request $req, Response $res) {
      /** @var CreateUserDto $dto */
      $dto = $req->validated;

      return $res->success([
         'email' => $dto->email,
         'name' => $dto->name ?? null,
      ]);
   });
});

$app->run();
