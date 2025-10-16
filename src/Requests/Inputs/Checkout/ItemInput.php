<?php

declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Inputs\Checkout;

use Zonos\ZonosSdk\Data\Checkout\Enums\CountryCode;
use Zonos\ZonosSdk\Data\Checkout\Enums\CurrencyCode;
use Zonos\ZonosSdk\Data\Checkout\Enums\DutyTaxFeeConfiguration;
use Zonos\ZonosSdk\Data\Checkout\Enums\ItemType;
use Zonos\ZonosSdk\Data\Checkout\Enums\ItemValueSource;

class ItemInput
{
  /**
   * @var ItemAttributeInput[]|null $attributes
   * @var ItemMeasurementInput[]|null $measurements
   * @var ItemMetadataInput[]|null $metadata
   * */
  public function __construct(
    public float                    $amount,
    public ?array                   $attributes,
    public ?CountryCode             $countryOfOrigin,
    public CurrencyCode             $currencyCode,
    public ?string                  $description,
    public ?DutyTaxFeeConfiguration $dutyTaxFeeConfiguration,
    public ?string                  $hsCode,
    public ?ItemValueSource         $hsCodeSource,
    public ?string                  $imageUrl,
    public ?ItemType                $itemType,
    public ?array                   $measurements,
    public ?array                   $metadata,
    public ?string                  $name,
    public ?string                  $productId,
    public ?string                  $provinceOfOrigin,
    public int                      $quantity,
    public ?string                  $referenceId,
    public ?string                  $rootId,
    public ?string                  $sku,
  ) {}

  public function toArray(): array
  {
    return [
      'amount' => $this->amount,
      'attributes' => $this->attributes,
      'countryOfOrigin' => $this->countryOfOrigin?->value,
      'currencyCode' => $this->currencyCode->value,
      'description' => $this->description,
      'dutyTaxFeeConfiguration' => $this->dutyTaxFeeConfiguration?->value,
      'hsCode' => $this->hsCode,
      'hsCodeSource' => $this->hsCodeSource?->value,
      'imageUrl' => $this->imageUrl,
      'itemType' => $this->itemType?->value,
      'measurements' => $this->measurements,
      'metadata' => $this->metadata,
      'name' => $this->name,
      'productId' => $this->productId,
      'provinceOfOrigin' => $this->provinceOfOrigin,
      'quantity' => $this->quantity,
      'referenceId' => $this->referenceId,
      'rootId' => $this->rootId,
      'sku' => $this->sku,
    ];
  }

  public static function fromArray(array $data): self
  {
    try {
      $measurements = null;
      if (isset($data['measurements'])) {
        $measurements = array_map(
          function ($item) {
            try {
              return ItemMeasurementInput::fromArray($item);
            } catch (\Exception $e) {
              throw new \RuntimeException('Error processing measurement: ' . json_encode($item) . ' - ' . $e->getMessage(), 0, $e);
            }
          },
          $data['measurements']
        );
      }

      $attributes = null;
      if (isset($data['attributes'])) {
        $attributes = array_map(
          function ($item) {
            try {
              return ItemAttributeInput::fromArray($item);
            } catch (\Exception $e) {
              throw new \RuntimeException('Error processing attribute: ' . json_encode($item) . ' - ' . $e->getMessage(), 0, $e);
            }
          },
          $data['attributes']
        );
      }

      $metadata = null;
      if (isset($data['metadata'])) {
        $metadata = array_map(
          function ($item) {
            try {
              return ItemMetadataInput::fromArray($item);
            } catch (\Exception $e) {
              throw new \RuntimeException('Error processing metadata: ' . json_encode($item) . ' - ' . $e->getMessage(), 0, $e);
            }
          },
          $data['metadata']
        );
      }

      if (!isset($data['currencyCode'])) {
        throw new \RuntimeException('Missing required field: currencyCode');
      }

      try {
        $currencyCode = CurrencyCode::from($data['currencyCode']);
      } catch (\ValueError $e) {
        throw new \RuntimeException('Invalid CurrencyCode: "' . $data['currencyCode'] . '". This currency code is not supported.');
      } catch (\Exception $e) {
        throw new \RuntimeException('Error creating CurrencyCode from: "' . $data['currencyCode'] . '" - ' . $e->getMessage(), 0, $e);
      }

      return new self(
        amount: $data['amount'] ?? 0,
        attributes: $attributes,
        countryOfOrigin: isset($data['countryOfOrigin']) ? CountryCode::from($data['countryOfOrigin']) : null,
        currencyCode: $currencyCode,
        description: $data['description'] ?? null,
        dutyTaxFeeConfiguration: isset($data['dutyTaxFeeConfiguration']) ? DutyTaxFeeConfiguration::from($data['dutyTaxFeeConfiguration']) : null,
        hsCode: $data['hsCode'] ?? null,
        hsCodeSource: isset($data['hsCodeSource']) ? ItemValueSource::from($data['hsCodeSource']) : null,
        imageUrl: $data['imageUrl'] ?? null,
        itemType: isset($data['itemType']) ? ItemType::from($data['itemType']) : null,
        measurements: $measurements,
        metadata: $metadata,
        name: $data['name'] ?? null,
        productId: $data['productId'] ?? null,
        provinceOfOrigin: $data['provinceOfOrigin'] ?? null,
        quantity: isset($data['quantity']) ? (int)preg_replace('/\D/', '', (string)$data['quantity']) : 0,
        referenceId: $data['referenceId'] ?? null,
        rootId: $data['rootId'] ?? null,
        sku: $data['sku'] ?? null,
      );
    } catch (\Exception $e) {
      throw new \RuntimeException('Error in ItemInput::fromArray with data: ' . json_encode($data) . ' - ' . $e->getMessage(), 0, $e);
    }
  }
}
