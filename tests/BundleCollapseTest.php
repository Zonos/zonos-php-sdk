<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Tests;

use PHPUnit\Framework\TestCase;
use Zonos\ZonosSdk\Data\Checkout\Item;
use Zonos\ZonosSdk\Data\Checkout\Metadata;

/**
 * Tests for bundle collapse logic in WordPressService.
 *
 * Since getMetadataValue, isBundleChildItem, and groupByBundleParent are private,
 * we use reflection to test them directly against the real class.
 */
class BundleCollapseTest extends TestCase
{
  private \ReflectionMethod $getMetadataValue;
  private \ReflectionMethod $isBundleChildItem;
  private \ReflectionMethod $groupByBundleParent;
  private object $service;

  protected function setUp(): void
  {
    // Create a minimal WordPressService without its real dependencies.
    // We use reflection to bypass the constructor and test private methods.
    $refClass = new \ReflectionClass(\Zonos\ZonosSdk\Services\WordPressService::class);
    $this->service = $refClass->newInstanceWithoutConstructor();

    $this->getMetadataValue = $refClass->getMethod('getMetadataValue');
    $this->isBundleChildItem = $refClass->getMethod('isBundleChildItem');
    $this->groupByBundleParent = $refClass->getMethod('groupByBundleParent');
  }

  // ── Helper ──────────────────────────────────────────

  private function makeItem(string $sku, float $amount, int $qty, array $metadataPairs = []): Item
  {
    $metadata = array_map(
      fn(array $pair) => new Metadata($pair[0], $pair[1]),
      $metadataPairs
    );

    return new Item(
      amount: $amount,
      attributes: [],
      currencyCode: 'USD',
      productId: '',
      quantity: $qty,
      sku: $sku,
      metadata: $metadata,
    );
  }

  private function makeBundleChild(string $sku, float $amount, int $qty, string $parentSku = 'bundle-abc', string $parentProductId = '12345', string $parentAmount = '50', string $parentQuantity = '1'): Item
  {
    return $this->makeItem($sku, $amount, $qty, [
      ['parentSku', $parentSku],
      ['parentProductId', $parentProductId],
      ['parentAmount', $parentAmount],
      ['parentQuantity', $parentQuantity],
    ]);
  }

  // ── getMetadataValue ────────────────────────────────

  public function testGetMetadataValue_findsKey(): void
  {
    $metadata = [new Metadata('parentSku', 'bundle-abc')];
    $result = $this->getMetadataValue->invoke($this->service, $metadata, 'parentSku');
    $this->assertSame('bundle-abc', $result);
  }

  public function testGetMetadataValue_returnsNullWhenKeyMissing(): void
  {
    $metadata = [new Metadata('parentSku', 'bundle-abc')];
    $result = $this->getMetadataValue->invoke($this->service, $metadata, 'nonExistentKey');
    $this->assertNull($result);
  }

  public function testGetMetadataValue_returnsNullForEmptyArray(): void
  {
    $result = $this->getMetadataValue->invoke($this->service, [], 'parentSku');
    $this->assertNull($result);
  }

  public function testGetMetadataValue_returnsEmptyStringValue(): void
  {
    $metadata = [new Metadata('parentSku', '')];
    $result = $this->getMetadataValue->invoke($this->service, $metadata, 'parentSku');
    $this->assertSame('', $result);
  }

  public function testGetMetadataValue_findsAmongMultipleKeys(): void
  {
    $metadata = [
      new Metadata('parentSku', 'bundle-abc'),
      new Metadata('parentAmount', '50'),
      new Metadata('parentQuantity', '1'),
    ];
    $result = $this->getMetadataValue->invoke($this->service, $metadata, 'parentAmount');
    $this->assertSame('50', $result);
  }

  // ── isBundleChildItem ───────────────────────────────

  public function testIsBundleChildItem_trueWithParentSku(): void
  {
    $item = $this->makeBundleChild('hat', 30, 1);
    $this->assertTrue($this->isBundleChildItem->invoke($this->service, $item));
  }

  public function testIsBundleChildItem_trueWithProductIdOnly(): void
  {
    $item = $this->makeBundleChild('hat', 30, 1, parentSku: '', parentProductId: '12345');
    $this->assertTrue($this->isBundleChildItem->invoke($this->service, $item));
  }

  public function testIsBundleChildItem_falseWithNoParentMetadata(): void
  {
    $item = $this->makeItem('shirt', 25, 1);
    $this->assertFalse($this->isBundleChildItem->invoke($this->service, $item));
  }

  public function testIsBundleChildItem_falseWithEmptyStrings(): void
  {
    $item = $this->makeBundleChild('hat', 30, 1, parentSku: '', parentProductId: '');
    $this->assertFalse($this->isBundleChildItem->invoke($this->service, $item));
  }

  public function testIsBundleChildItem_trueWithBothPresent(): void
  {
    $item = $this->makeBundleChild('hat', 30, 1, parentSku: 'bundle-abc', parentProductId: '12345');
    $this->assertTrue($this->isBundleChildItem->invoke($this->service, $item));
  }

  // ── groupByBundleParent ─────────────────────────────

  public function testGroupByBundleParent_allStandalone(): void
  {
    $items = [
      $this->makeItem('shirt', 25, 1),
      $this->makeItem('pants', 40, 2),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    $this->assertEmpty($bundles);
    $this->assertCount(2, $standalone);
  }

  public function testGroupByBundleParent_allBundleChildren(): void
  {
    $items = [
      $this->makeBundleChild('hat', 30, 1),
      $this->makeBundleChild('scarf', 20, 1),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    $this->assertEmpty($standalone);
    $this->assertCount(1, $bundles);
    $this->assertArrayHasKey('bundle-abc', $bundles);
    $this->assertCount(2, $bundles['bundle-abc']['children']);
    $this->assertSame(50.0, $bundles['bundle-abc']['parentAmount']);
    $this->assertSame(1, $bundles['bundle-abc']['parentQuantity']);
    $this->assertSame('12345', $bundles['bundle-abc']['parentProductId']);
  }

  public function testGroupByBundleParent_mixedCart(): void
  {
    $items = [
      $this->makeBundleChild('hat', 30, 1),
      $this->makeBundleChild('scarf', 20, 1),
      $this->makeItem('shirt', 25, 1),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    $this->assertCount(1, $bundles);
    $this->assertCount(2, $bundles['bundle-abc']['children']);
    $this->assertCount(1, $standalone);
    $this->assertSame('shirt', $standalone[0]->sku);
  }

  public function testGroupByBundleParent_multipleBundles(): void
  {
    $items = [
      $this->makeBundleChild('hat', 30, 1, parentSku: 'bundle-abc', parentProductId: '111', parentAmount: '50', parentQuantity: '1'),
      $this->makeBundleChild('scarf', 20, 1, parentSku: 'bundle-abc', parentProductId: '111', parentAmount: '50', parentQuantity: '1'),
      $this->makeBundleChild('glove', 15, 1, parentSku: 'bundle-xyz', parentProductId: '222', parentAmount: '30', parentQuantity: '2'),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    $this->assertEmpty($standalone);
    $this->assertCount(2, $bundles);

    $this->assertCount(2, $bundles['bundle-abc']['children']);
    $this->assertSame(50.0, $bundles['bundle-abc']['parentAmount']);

    $this->assertCount(1, $bundles['bundle-xyz']['children']);
    $this->assertSame(30.0, $bundles['bundle-xyz']['parentAmount']);
    $this->assertSame(2, $bundles['bundle-xyz']['parentQuantity']);
  }

  public function testGroupByBundleParent_productIdOnlyGrouping(): void
  {
    $items = [
      $this->makeBundleChild('hat', 30, 1, parentSku: '', parentProductId: '12345'),
      $this->makeBundleChild('scarf', 20, 1, parentSku: '', parentProductId: '12345'),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    $this->assertEmpty($standalone);
    $this->assertCount(1, $bundles);
    // Grouped by productId since SKU is empty
    $this->assertArrayHasKey('12345', $bundles);
    $this->assertCount(2, $bundles['12345']['children']);
    $this->assertSame('', $bundles['12345']['parentSku']);
    $this->assertSame('12345', $bundles['12345']['parentProductId']);
  }

  public function testGroupByBundleParent_skuPreferredOverProductId(): void
  {
    $items = [
      $this->makeBundleChild('hat', 30, 1, parentSku: 'bundle-abc', parentProductId: '12345'),
    ];

    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, $items);

    // Should be keyed by SKU, not productId
    $this->assertArrayHasKey('bundle-abc', $bundles);
    $this->assertArrayNotHasKey('12345', $bundles);
  }

  public function testGroupByBundleParent_emptyItemsList(): void
  {
    [$bundles, $standalone] = $this->groupByBundleParent->invoke($this->service, []);

    $this->assertEmpty($bundles);
    $this->assertEmpty($standalone);
  }
}
