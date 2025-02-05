<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Checkout;

class Person
{
  public function __construct(
    public string $companyName,
    public string $email,
    public string $firstName,
    public string $lastName,
    public string $phone,
  ) {
  }

  public function toArray(): array
  {
    return [
      'companyName' => $this->companyName,
      'email' => $this->email,
      'firstName' => $this->firstName,
      'lastName' => $this->lastName,
      'phone' => $this->phone,
    ];
  }

  public static function fromArray(array $data): self
  {
    return new self(
      companyName: $data['companyName'] ?? '',
      email:       $data['email'] ?? '',
      firstName:   $data['firstName'] ?? '',
      lastName:    $data['lastName'] ?? '',
      phone:       $data['phone'] ?? '',
    );
  }
}