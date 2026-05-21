<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

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

interface InventoryServiceInterface
{
    /**
     * @param array<string, string> $headers
     * @return CategoryListResponse|array<string, mixed>
     */
    public function getCategories(array $headers = []): CategoryListResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getCategoriesAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return CategoryProductList|array<string, mixed>
     */
    public function getCategory(int $categoryId, array $headers = []): CategoryProductList|array;

    /**
     * @param array<string, string> $headers
     */
    public function getCategoryAsync(int $categoryId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return CategoryCreatedResponse|array<string, mixed>
     */
    public function createCategory(CategoryCreateRequest $request, array $headers = []): CategoryCreatedResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function createCategoryAsync(CategoryCreateRequest $request, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateCategory(int $categoryId, CategoryCreateRequest $request, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateCategoryAsync(int $categoryId, CategoryCreateRequest $request, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteCategory(int $categoryId, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteCategoryAsync(int $categoryId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function createProduct(ProductAddDetail $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function createProductAsync(ProductAddDetail $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function updateProduct(string $productId, ProductPatchDetail $details, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function updateProductAsync(string $productId, ProductPatchDetail $details, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return InventorySummaryResponse|array<string, mixed>
     */
    public function getProducts(?GetProductsRequest $request = null, array $headers = []): InventorySummaryResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getProductsAsync(?GetProductsRequest $request = null, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return ProductDetail|array<string, mixed>
     */
    public function getProduct(string $productId, array $headers = []): ProductDetail|array;

    /**
     * @param array<string, string> $headers
     */
    public function getProductAsync(string $productId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function deleteProduct(string $productId, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function deleteProductAsync(string $productId, array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     * @return FullInventoryDetailsResponse|array<string, mixed>
     */
    public function getPos(array $headers = []): FullInventoryDetailsResponse|array;

    /**
     * @param array<string, string> $headers
     */
    public function getPosAsync(array $headers = []): mixed;

    /**
     * @param array<string, string> $headers
     */
    public function lockProduct(string $productId, LockRequest $request, array $headers = []): void;

    /**
     * @param array<string, string> $headers
     */
    public function lockProductAsync(string $productId, LockRequest $request, array $headers = []): mixed;
}
