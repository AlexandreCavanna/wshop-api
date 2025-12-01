<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Store;
use App\Service\StoreManager;
use App\Service\StoreSearch;
use Nelmio\ApiDocBundle\Attribute\Areas;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Areas(['default'])]
#[OA\Tag(name: 'Store')]
class StoreController extends AbstractController
{
    public function __construct(
        private readonly StoreSearch $storeSearch,
        private readonly StoreManager $storeManager
    ) {
    }

    #[Route('/api/stores', name: 'store_index', methods: ['GET'])]
    #[OA\Get(description: 'Returns a list of stores with filtering and sorting options.', summary: 'Store list')]
    #[OA\Parameter(
        name: 'city',
        description: 'Filter by city (exact match).',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: 'Paris')
    )]
    #[OA\Parameter(
        name: 'name',
        description: 'Filter by name (partial match).',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: 'Wshop')
    )]
    #[OA\Parameter(
        name: 'postalCode',
        description: 'Filter by postal code (exact match).',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: '75001')
    )]
    #[OA\Parameter(
        name: 'sort',
        description: 'Sort field (name, city, createdAt, updatedAt).',
        in: 'query',
        schema: new OA\Schema(type: 'string', example: 'name')
    )]
    #[OA\Parameter(
        name: 'direction',
        description: 'Sorting direction (ASC or DESC).',
        in: 'query',
        schema: new OA\Schema(type: 'string', default: 'ASC', example: 'ASC')
    )]
    #[OA\Response(
        response: 200,
        description: 'List of stores.',
        content: new Model(type: Store::class, groups: ['store:read'])
    )]
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'city' => $request->query->get('city'),
            'name' => $request->query->get('name'),
            'postalCode' => $request->query->get('postalCode'),
        ];

        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'ASC');

        $stores = $this->storeSearch->searchStoresByFilters($filters, $sort, $direction);

        return $this->json($stores);
    }

    #[Route('/api/stores/{id}', name: 'store_show', methods: ['GET'])]
    #[OA\Get(description: 'Returns the details of a store.', summary: 'Store details')]
    #[OA\Response(
        response: 200,
        description: 'Store found.',
        content: new Model(type: Store::class, groups: ['store:read'])
    )]
    #[OA\Response(response: 404, description: 'Store not found.')]
    public function show(Store $store): JsonResponse
    {
        return $this->json($store);
    }

    #[Route('/api/stores', name: 'store_create', methods: ['POST'])]
    #[OA\Post(description: 'Creates a new store.', summary: 'Create a store')]
    #[OA\RequestBody(
        description: 'Data for creating the store.',
        required: true,
        content: new Model(type: Store::class, groups: ['store:write'])
    )]
    #[OA\Response(
        response: 201,
        description: 'Store created.',
        content: new Model(type: Store::class, groups: ['store:read'])
    )]
    #[OA\Response(response: 400, description: 'Invalid data.')]
    public function create(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], 400);
        }

        if (! is_array($payload)) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], 400);
        }

        try {
            /**
             * @var array{
             *     name?: string,
             *     address?: string,
             *     city?: string,
             *     postalCode?: string
             * } $payload
             */
            $store = $this->storeManager->createStore($payload);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            return $this->json([
                'error' => $invalidArgumentException->getMessage(),
            ], 400);
        }

        return $this->json($store, 201);
    }

    #[Route('/api/stores/{id}', name: 'store_update', methods: ['PATCH'])]
    #[OA\Patch(description: 'Updates an existing store.', summary: 'Update a store')]
    #[OA\RequestBody(
        description: 'Fields to update.',
        required: false,
        content: new Model(type: Store::class, groups: ['store:write'])
    )]
    #[OA\Response(
        response: 200,
        description: 'Store updated.',
        content: new Model(type: Store::class, groups: ['store:read'])
    )]
    #[OA\Response(response: 400, description: 'Invalid data.')]
    public function update(Store $store, Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], 400);
        }

        if (! is_array($payload)) {
            return $this->json([
                'error' => 'Invalid JSON',
            ], 400);
        }

        /**
         * @var array{
         *     name?: string,
         *     address?: string,
         *     city?: string,
         *     postalCode?: string
         * } $payload
         */
        $store = $this->storeManager->updateStore($store, $payload);

        return $this->json($store);
    }

    #[Route('/api/stores/{id}', name: 'store_delete', methods: ['DELETE'])]
    #[OA\Delete(description: 'Deletes a store.', summary: 'Delete a store')]
    #[OA\Response(response: 204, description: 'Store deleted.')]
    public function delete(Store $store): JsonResponse
    {
        $this->storeManager->deleteStore($store);

        return $this->json(null, 204);
    }
}
