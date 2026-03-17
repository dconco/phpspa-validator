<?php

declare(strict_types=1);

namespace PhpSPA\Validator;

/**
 * @template T of object
 */
final class ValidationResult
{
   /**
    * @param array<string, mixed> $errors
    * @param class-string<T>|T|null $data
    */
   public function __construct(
      private readonly string $message,
      private readonly array $errors,
      private readonly ?object $data
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

   /** @return ?T */
   public function data(): ?object
   {
      return $this->data;
   }
}
