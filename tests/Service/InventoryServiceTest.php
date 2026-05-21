<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Tests\Service;

use MirrorPS\TalerBundle\Service\InventoryService;
use MirrorPS\TalerBundle\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\Inventory\Dto\CategoryCreateRequest;
use Taler\Api\Inventory\Dto\CategoryCreatedResponse;
use Taler\Api\Inventory\Dto\CategoryListResponse;
use Taler\Api\Inventory\Dto\CategoryProductList;
use Taler\Api\Inventory\Dto\FullInventoryDetailsResponse;
use Taler\Api\Inventory\Dto\GetProductsRequest;
use Taler\Api\Inventory\Dto\InventorySummaryResponse;
use Taler\Api\Inventory\Dto\LockRequest;
use Taler\Api\Inventory\Dto\ProductAddDetail;
use Taler\Api\Inventory\Dto\ProductDetail;
use Taler\Api\Inventory\Dto\ProductPatchDetail;
use Taler\Api\Dto\RelativeTime;
use Taler\Api\Inventory\InventoryClient;
use Taler\Taler as TalerClient;

final class InventoryServiceTest extends TestCase
{
    private InventoryClient $inventoryClient;
    private InventoryService $service;

    protected function setUp(): void
    {
        $this->inventoryClient = $this->createMock(InventoryClient::class);

        $client = $this->createMock(TalerClient::class);
        $client->method('inventory')->willReturn($this->inventoryClient);

        $taler = new Taler($client);
        $this->service = new InventoryService($taler);
    }

    public function testGetCategoriesDelegatesToClient(): void
    {
        $expected = new CategoryListResponse([]);

        $this->inventoryClient->expects(self::once())
            ->method('getCategories')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getCategories());
    }

    public function testGetCategoriesPassesHeaders(): void
    {
        $headers = ['Accept' => 'application/json'];
        $expected = new CategoryListResponse([]);

        $this->inventoryClient->expects(self::once())
            ->method('getCategories')
            ->with($headers)
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getCategories($headers));
    }

    public function testGetCategoriesAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('getCategoriesAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getCategoriesAsync());
    }

    public function testGetCategoryDelegatesToClient(): void
    {
        $categoryId = 3;
        $expected = new CategoryProductList('Drinks', null, []);

        $this->inventoryClient->expects(self::once())
            ->method('getCategory')
            ->with($categoryId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getCategory($categoryId));
    }

    public function testGetCategoryAsyncDelegatesToClient(): void
    {
        $categoryId = 2;
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('getCategoryAsync')
            ->with($categoryId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getCategoryAsync($categoryId));
    }

    public function testCreateCategoryDelegatesToClient(): void
    {
        $request = new CategoryCreateRequest(name: 'Beverages');
        $expected = new CategoryCreatedResponse(category_id: 1);

        $this->inventoryClient->expects(self::once())
            ->method('createCategory')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createCategory($request));
    }

    public function testCreateCategoryAsyncDelegatesToClient(): void
    {
        $request = new CategoryCreateRequest(name: 'Snacks');
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('createCategoryAsync')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createCategoryAsync($request));
    }

    public function testUpdateCategoryDelegatesToClient(): void
    {
        $categoryId = 5;
        $request = new CategoryCreateRequest(name: 'Drinks');

        $this->inventoryClient->expects(self::once())
            ->method('updateCategory')
            ->with($categoryId, $request, []);

        $this->service->updateCategory($categoryId, $request);
    }

    public function testUpdateCategoryAsyncDelegatesToClient(): void
    {
        $categoryId = 4;
        $request = new CategoryCreateRequest(name: 'Food');
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('updateCategoryAsync')
            ->with($categoryId, $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateCategoryAsync($categoryId, $request));
    }

    public function testDeleteCategoryDelegatesToClient(): void
    {
        $categoryId = 9;

        $this->inventoryClient->expects(self::once())
            ->method('deleteCategory')
            ->with($categoryId, []);

        $this->service->deleteCategory($categoryId);
    }

    public function testDeleteCategoryAsyncDelegatesToClient(): void
    {
        $categoryId = 8;
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('deleteCategoryAsync')
            ->with($categoryId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteCategoryAsync($categoryId));
    }

    public function testCreateProductDelegatesToClient(): void
    {
        $details = new ProductAddDetail(
            product_id: 'coffee-1kg',
            description: 'Arabica beans 1kg',
            unit: 'kg',
            price: 'EUR:12.50',
            total_stock: 100,
        );

        $this->inventoryClient->expects(self::once())
            ->method('createProduct')
            ->with($details, []);

        $this->service->createProduct($details);
    }

    public function testCreateProductAsyncDelegatesToClient(): void
    {
        $details = new ProductAddDetail(
            product_id: 'tea-500g',
            description: 'Green tea',
            unit: 'g',
            price: 'EUR:5.00',
            total_stock: 50,
        );
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('createProductAsync')
            ->with($details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->createProductAsync($details));
    }

    public function testUpdateProductDelegatesToClient(): void
    {
        $productId = 'coffee-1kg';
        $details = new ProductPatchDetail(
            description: 'Updated',
            unit: 'kg',
            price: 'EUR:13.00',
            total_stock: 120,
        );

        $this->inventoryClient->expects(self::once())
            ->method('updateProduct')
            ->with($productId, $details, []);

        $this->service->updateProduct($productId, $details);
    }

    public function testUpdateProductAsyncDelegatesToClient(): void
    {
        $productId = 'tea-500g';
        $details = new ProductPatchDetail(
            description: 'Updated tea',
            unit: 'g',
            price: 'EUR:5.50',
            total_stock: 60,
        );
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('updateProductAsync')
            ->with($productId, $details, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->updateProductAsync($productId, $details));
    }

    public function testGetProductsDelegatesToClient(): void
    {
        $request = new GetProductsRequest(limit: 20);
        $expected = new InventorySummaryResponse([]);

        $this->inventoryClient->expects(self::once())
            ->method('getProducts')
            ->with($request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getProducts($request));
    }

    public function testGetProductsWithNullRequestDelegatesToClient(): void
    {
        $expected = new InventorySummaryResponse([]);

        $this->inventoryClient->expects(self::once())
            ->method('getProducts')
            ->with(null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getProducts());
    }

    public function testGetProductsAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('getProductsAsync')
            ->with(null, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getProductsAsync());
    }

    public function testGetProductDelegatesToClient(): void
    {
        $productId = 'coffee-1kg';
        $expected = new ProductDetail(
            product_name: 'Coffee',
            description: 'Beans',
            description_i18n: [],
            unit: 'kg',
            categories: [],
            price: 'EUR:12.50',
            image: '',
            taxes: null,
            total_stock: 100,
            total_sold: 0,
            total_lost: 0,
            address: null,
            next_restock: null,
            minimum_age: null,
        );

        $this->inventoryClient->expects(self::once())
            ->method('getProduct')
            ->with($productId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getProduct($productId));
    }

    public function testGetProductAsyncDelegatesToClient(): void
    {
        $productId = 'tea-500g';
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('getProductAsync')
            ->with($productId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getProductAsync($productId));
    }

    public function testDeleteProductDelegatesToClient(): void
    {
        $productId = 'obsolete-item';

        $this->inventoryClient->expects(self::once())
            ->method('deleteProduct')
            ->with($productId, []);

        $this->service->deleteProduct($productId);
    }

    public function testDeleteProductAsyncDelegatesToClient(): void
    {
        $productId = 'old-item';
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('deleteProductAsync')
            ->with($productId, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->deleteProductAsync($productId));
    }

    public function testGetPosDelegatesToClient(): void
    {
        $expected = new FullInventoryDetailsResponse([], []);

        $this->inventoryClient->expects(self::once())
            ->method('getPos')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getPos());
    }

    public function testGetPosAsyncDelegatesToClient(): void
    {
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('getPosAsync')
            ->with([])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->getPosAsync());
    }

    public function testLockProductDelegatesToClient(): void
    {
        $productId = 'coffee-1kg';
        $request = new LockRequest(
            lock_uuid: '123e4567-e89b-12d3-a456-426614174000',
            duration: new RelativeTime(60_000_000),
            quantity: 2,
        );

        $this->inventoryClient->expects(self::once())
            ->method('lockProduct')
            ->with($productId, $request, []);

        $this->service->lockProduct($productId, $request);
    }

    public function testLockProductAsyncDelegatesToClient(): void
    {
        $productId = 'coffee-1kg';
        $request = new LockRequest(
            lock_uuid: '123e4567-e89b-12d3-a456-426614174001',
            duration: new RelativeTime(0),
            quantity: 0,
        );
        $expected = 'promise';

        $this->inventoryClient->expects(self::once())
            ->method('lockProductAsync')
            ->with($productId, $request, [])
            ->willReturn($expected);

        self::assertSame($expected, $this->service->lockProductAsync($productId, $request));
    }
}
