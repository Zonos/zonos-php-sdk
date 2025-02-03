<?php declare(strict_types=1);

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
  ) {
  }

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
    return new self(
      amount:                  $data['amount'] ?? 0,
      attributes:              isset($data['attributes']) ? array_map(
                                 fn($item) => ItemAttributeInput::fromArray($item),
                                 $data['attributes']
                               ) : null,
      countryOfOrigin:         isset($data['countryOfOrigin']) ? CountryCode::from($data['countryOfOrigin']) : null,
      currencyCode:            CurrencyCode::from($data['currencyCode']),
      description:             $data['description'] ?? null,
      dutyTaxFeeConfiguration: isset($data['dutyTaxFeeConfiguration']) ? DutyTaxFeeConfiguration::from($data['dutyTaxFeeConfiguration']) : null,
      hsCode:                  $data['hsCode'] ?? null,
      hsCodeSource:            isset($data['hsCodeSource']) ? ItemValueSource::from($data['hsCodeSource']) : null,
      imageUrl:                $data['imageUrl'] ?? null,
      itemType:                isset($data['itemType']) ? ItemType::from($data['itemType']) : null,
      measurements:            isset($data['measurements']) ? array_map(
                                 fn($item) => ItemMeasurementInput::fromArray($item),
                                 $data['measurements']
                               ) : null,
      metadata:                isset($data['metadata']) ? array_map(
                                 fn($item) => ItemMetadataInput::fromArray($item),
                                 $data['metadata']
                               ) : null,
      name:                    $data['name'] ?? null,
      productId:               $data['productId'] ?? null,
      provinceOfOrigin:        $data['provinceOfOrigin'] ?? null,
      quantity:                $data['quantity'] ?? 0,
      referenceId:             $data['referenceId'] ?? null,
      rootId:                  $data['rootId'] ?? null,
      sku:                     $data['sku'] ?? null,
    );
  }
}
