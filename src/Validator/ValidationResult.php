<?php

declare(strict_types=1);

namespace PhpSPA\Validator;

final class ValidationResult
{
   /** @param array<string, mixed> $errors */
   public function __construct(
      private readonly string $message,
      private readonly array $errors,
      private readonly ?Validatable $data
   ) {}

   public function isValid(): bool
   {
      return $this->errors === [];
   }

   public function message(): string
   {
      return $this->message;
   }

   /** @return array<string, mixed> */
   public function errors(): array
   {
      return $this->errors;
   }

   public function data(): ?Validatable
   {
      return $this->data;
   }
}
