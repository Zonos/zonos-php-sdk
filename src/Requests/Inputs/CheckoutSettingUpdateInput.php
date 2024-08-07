<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs;

class CheckoutSettingUpdateInput
{
    public string $allowedCharacterSets;
    public array $allowedDomains;
    public string $createdAt; // Implement date time
    public string $createdBy;
    public string $id;
    public string $mode; // Implement enums
    public string $organization;
    public string $placeOrderButtonSelector;
    public string $status;
    public string $subscriptionStatus;
    public string $successBehavior;
    public string $successRedirectUrl;
    public string $updatedAt;
    public string $updatedBy;
    public string $visibilityStatus;

    public function __construct(
        string $allowedCharacterSets,
        array $allowedDomains,
        string $createdAt,
        string $createdBy,
        string $id,
        string $mode,
        string $organization,
        string $placeOrderButtonSelector,
        string $status,
        string $subscriptionStatus,
        string $successBehavior,
        string $successRedirectUrl,
        string $updatedAt,
        string $updatedBy,
        string $visibilityStatus,
    )
    {
        $this->allowedCharacterSets = $allowedCharacterSets;
        $this->allowedDomains = $allowedDomains;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->id = $id;
        $this->mode = $mode;
        $this->organization = $organization;
        $this->placeOrderButtonSelector = $placeOrderButtonSelector;
        $this->status = $status;
        $this->subscriptionStatus = $subscriptionStatus;
        $this->successBehavior = $successBehavior;
        $this->successRedirectUrl = $successRedirectUrl;
        $this->updatedAt = $updatedAt;
        $this->updatedBy = $updatedBy;
        $this->visibilityStatus = $visibilityStatus;
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
        $allowedCharacterSets = $data['allowedCharacterSets'] ?? '';
        $allowedDomains = $data['allowedDomains'] ?? [];
        $createdAt = $data['createdAt'] ?? '';
        $createdBy = $data['createdBy'] ?? '';
        $id = $data['id'] ?? '';
        $mode = $data['mode'] ?? '';
        $organization = $data['organization'] ?? '';
        $placeOrderButtonSelector = $data['placeOrderButtonSelector'] ?? '';
        $status = $data['status'] ?? '';
        $subscriptionStatus = $data['subscriptionStatus'] ?? '';
        $successBehavior = $data['successBehavior'] ?? '';
        $successRedirectUrl = $data['successRedirectUrl'] ?? '';
        $updatedAt = $data['updatedAt'] ?? '';
        $updatedBy = $data['updatedBy'] ?? '';
        $visibilityStatus = $data['visibilityStatus'] ?? '';

        return new self(
            $allowedCharacterSets,
            $allowedDomains,
            $createdAt,
            $createdBy,
            $id,
            $mode,
            $organization,
            $placeOrderButtonSelector,
            $status,
            $subscriptionStatus,
            $successBehavior,
            $successRedirectUrl,
            $updatedAt,
            $updatedBy,
            $visibilityStatus,
        );
    }
}