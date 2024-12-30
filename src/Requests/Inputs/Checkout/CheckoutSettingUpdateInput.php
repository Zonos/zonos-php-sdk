<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

class CheckoutSettingUpdateInput
{
  public function __construct(
    public string $allowedCharacterSets,
    public array  $allowedDomains,
    public string $createdAt,
    public string $createdBy,
    public string $id,
    public string $mode,
    public string $organization,
    public string $placeOrderButtonSelector,
    public string $status,
    public string $subscriptionStatus,
    public string $successBehavior,
    public string $successRedirectUrl,
    public string $updatedAt,
    public string $updatedBy,
    public string $visibilityStatus,
  ) {
  }

  public function toArray(): array
  {
    return [
      'allowedCharacterSets' => $this->allowedCharacterSets,
      'allowedDomains' => $this->allowedDomains,
      'createdAt' => $this->createdAt,
      'createdBy' => $this->createdBy,
      'id' => $this->id,
      'mode' => $this->mode,
      'organization' => $this->organization,
      'placeOrderButtonSelector' => $this->placeOrderButtonSelector,
      'status' => $this->status,
      'subscriptionStatus' => $this->subscriptionStatus,
      'successBehavior' => $this->successBehavior,
      'successRedirectUrl' => $this->successRedirectUrl,
      'updatedAt' => $this->updatedAt,
      'updatedBy' => $this->updatedBy,
      'visibilityStatus' => $this->visibilityStatus,
    ];
  }

  public static function fromArray(array $data): self // May be abstracted
  {
    return new self(
      allowedCharacterSets:     $data['allowedCharacterSets'] ?? '',
      allowedDomains:           $data['allowedDomains'] ?? [],
      createdAt:                $data['createdAt'] ?? '',
      createdBy:                $data['createdBy'] ?? '',
      id:                       $data['id'] ?? '',
      mode:                     $data['mode'] ?? '',
      organization:             $data['organization'] ?? '',
      placeOrderButtonSelector: $data['placeOrderButtonSelector'] ?? '',
      status:                   $data['status'] ?? '',
      subscriptionStatus:       $data['subscriptionStatus'] ?? '',
      successBehavior:          $data['successBehavior'] ?? '',
      successRedirectUrl:       $data['successRedirectUrl'] ?? '',
      updatedAt:                $data['updatedAt'] ?? '',
      updatedBy:                $data['updatedBy'] ?? '',
      visibilityStatus:         $data['visibilityStatus'] ?? '',
    );
  }
}