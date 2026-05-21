<?php

declare(strict_types=1);

namespace MirrorPS\TalerBundle\Service;

use MirrorPS\TalerBundle\Taler;
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

final class InventoryService implements InventoryServiceInterface
{
    public function __construct(private readonly Taler $taler)
    {
    }

    public function getCategories(array $headers = []): CategoryListResponse|array
    {
        return $this->taler->inventory()->getCategories($headers);
    }

    public function getCategoriesAsync(array $headers = []): mixed
    {
        return $this->taler->inventory()->getCategoriesAsync($headers);
    }

    public function getCategory(int $categoryId, array $headers = []): CategoryProductList|array
    {
        return $this->taler->inventory()->getCategory($categoryId, $headers);
    }

    public function getCategoryAsync(int $categoryId, array $headers = []): mixed
    {
        return $this->taler->inventory()->getCategoryAsync($categoryId, $headers);
    }

    public function createCategory(CategoryCreateRequest $request, array $headers = []): CategoryCreatedResponse|array
    {
        return $this->taler->inventory()->createCategory($request, $headers);
    }

    public function createCategoryAsync(CategoryCreateRequest $request, array $headers = []): mixed
    {
        return $this->taler->inventory()->createCategoryAsync($request, $headers);
    }

    public function updateCategory(int $categoryId, CategoryCreateRequest $request, array $headers = []): void
    {
        $this->taler->inventory()->updateCategory($categoryId, $request, $headers);
    }

    public function updateCategoryAsync(int $categoryId, CategoryCreateRequest $request, array $headers = []): mixed
    {
        return $this->taler->inventory()->updateCategoryAsync($categoryId, $request, $headers);
    }

    public function deleteCategory(int $categoryId, array $headers = []): void
    {
        $this->taler->inventory()->deleteCategory($categoryId, $headers);
    }

    public function deleteCategoryAsync(int $categoryId, array $headers = []): mixed
    {
        return $this->taler->inventory()->deleteCategoryAsync($categoryId, $headers);
    }

    public function createProduct(ProductAddDetail $details, array $headers = []): void
    {
        $this->taler->inventory()->createProduct($details, $headers);
    }

    public function createProductAsync(ProductAddDetail $details, array $headers = []): mixed
    {
        return $this->taler->inventory()->createProductAsync($details, $headers);
    }

    public function updateProduct(string $productId, ProductPatchDetail $details, array $headers = []): void
    {
        $this->taler->inventory()->updateProduct($productId, $details, $headers);
    }

    public function updateProductAsync(string $productId, ProductPatchDetail $details, array $headers = []): mixed
    {
        return $this->taler->inventory()->updateProductAsync($productId, $details, $headers);
    }

    public function getProducts(?GetProductsRequest $request = null, array $headers = []): InventorySummaryResponse|array
    {
        return $this->taler->inventory()->getProducts($request, $headers);
    }

    public function getProductsAsync(?GetProductsRequest $request = null, array $headers = []): mixed
    {
        return $this->taler->inventory()->getProductsAsync($request, $headers);
    }

    public function getProduct(string $productId, array $headers = []): ProductDetail|array
    {
        return $this->taler->inventory()->getProduct($productId, $headers);
    }

    public function getProductAsync(string $productId, array $headers = []): mixed
    {
        return $this->taler->inventory()->getProductAsync($productId, $headers);
    }

    public function deleteProduct(string $productId, array $headers = []): void
    {
        $this->taler->inventory()->deleteProduct($productId, $headers);
    }

    public function deleteProductAsync(string $productId, array $headers = []): mixed
    {
        return $this->taler->inventory()->deleteProductAsync($productId, $headers);
    }

    public function getPos(array $headers = []): FullInventoryDetailsResponse|array
    {
        return $this->taler->inventory()->getPos($headers);
    }

    public function getPosAsync(array $headers = []): mixed
    {
        return $this->taler->inventory()->getPosAsync($headers);
    }

    public function lockProduct(string $productId, LockRequest $request, array $headers = []): void
    {
        $this->taler->inventory()->lockProduct($productId, $request, $headers);
    }

    public function lockProductAsync(string $productId, LockRequest $request, array $headers = []): mixed
    {
        return $this->taler->inventory()->lockProductAsync($productId, $request, $headers);
    }
}
