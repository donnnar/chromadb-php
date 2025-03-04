<?php

declare(strict_types=1);


namespace Codewithkyrian\ChromaDB\Generated;

use Codewithkyrian\ChromaDB\Generated\Exceptions\ChromaConnectionException;
use Codewithkyrian\ChromaDB\Generated\Exceptions\ChromaException;
use Codewithkyrian\ChromaDB\Generated\Exceptions\ChromaNotFoundException;
use Codewithkyrian\ChromaDB\Generated\Models\Collection;
use Codewithkyrian\ChromaDB\Generated\Models\Database;
use Codewithkyrian\ChromaDB\Generated\Models\Tenant;
use Codewithkyrian\ChromaDB\Generated\Requests\AddEmbeddingRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\CreateCollectionRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\CreateDatabaseRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\CreateTenantRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\DeleteEmbeddingRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\GetEmbeddingRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\QueryEmbeddingRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\UpdateCollectionRequest;
use Codewithkyrian\ChromaDB\Generated\Requests\UpdateEmbeddingRequest;
use Codewithkyrian\ChromaDB\Generated\Responses\GetItemsResponse;
use Codewithkyrian\ChromaDB\Generated\Responses\QueryItemsResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Client for ChromaDB API (v.0.2.0)
 */
class ChromaApiClient
{

    public function __construct(
        public readonly Client $httpClient,
    ) {
    }

    public function root(): array
    {
        try {
            $response = $this->httpClient->get('/api/v2');
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
        return json_decode($response->getBody()->getContents(), true);
    }


    public function version(): string
    {
        try {
            $response = $this->httpClient->get('/api/v2/version');

            // remove the quo
            return trim($response->getBody()->getContents(), '"');
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function heartbeat(): array
    {
        try {
            $response = $this->httpClient->get('/api/v2/heartbeat');
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    public function preFlightChecks(): mixed
    {
        try {
            $response = $this->httpClient->get('/api/v2/pre-flight-checks');
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
        return json_decode($response->getBody()->getContents(), true);
    }


    public function createDatabase(string $tenant, CreateDatabaseRequest $request): void
    {
        try {
            $this->httpClient->post("/api/v2/tenants/$tenant/databases", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function getDatabase(string $database, string $tenant): Database
    {
        try {
            $response = $this->httpClient->get("/api/v2/tenants/$tenant/databases/$database");
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return Database::make($result);
    }

    public function createTenant(CreateTenantRequest $request): void
    {
        try {
            $this->httpClient->post('/api/v2/tenants', [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function getTenant(string $tenant): ?Tenant
    {
        try {
            $response = $this->httpClient->get("/api/v2/tenants/$tenant");

            $result = json_decode($response->getBody()->getContents(), true);

            return Tenant::make($result);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }


    public function listCollections(string $database, string $tenant): array
    {
        try {
            $response = $this->httpClient->get("/api/v2/tenants/$tenant/databases/$database/collections");
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return array_map(function (array $item) {
            return Collection::make($item);
        }, $result);
    }

    public function createCollection(string $database, string $tenant, CreateCollectionRequest $request): Collection
    {
        try {
            $response = $this->httpClient->post("/api/v2/tenants/$tenant/databases/$database/collections", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return Collection::make($result);
    }

    public function getCollection(string $collectionId, string $database, string $tenant): Collection
    {
        try {
            $response = $this->httpClient->get(
                "/api/v2/tenants/$tenant/databases/$database/collections/$collectionId/get"
            );
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return Collection::make($result);
    }

    public function updateCollection(string $collectionId, UpdateCollectionRequest $request): void
    {
        try {
            $response = $this->httpClient->put("/api/v2/collections/$collectionId", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function deleteCollection(string $collectionId, string $database, string $tenant): void
    {
        try {
            $this->httpClient->delete("/api/v2/tenants/$tenant/databases/$database/collections/$collectionId/delete");
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function addToCollection(
        string $collectionId,
        string $database,
        string $tenant,
        AddEmbeddingRequest $request
    ): void {
        try {
            $this->httpClient->post("/api/v2/tenants/$tenant/databases/$database/collections/$collectionId/add", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function add(string $collectionId, AddEmbeddingRequest $request): void
    {
        try {
            // @todo veraltet!
//            $options = $request->toArray();
//            $this->httpClient->post("/api/v2/tenants/{tenant}/databases/{database_name}/collections/$collectionId/add", $options);
            $this->httpClient->post("/api/v2/collections/$collectionId/add", $request->toArray());
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function update(string $collectionId, UpdateEmbeddingRequest $request): void
    {
        try {
            $this->httpClient->post("/api/v2/collections/$collectionId/update", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function upsert(string $collectionId, AddEmbeddingRequest $request): void
    {
        try {
            $this->httpClient->post("/api/v2/collections/$collectionId/upsert", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function get(string $collectionId, GetEmbeddingRequest $request): GetItemsResponse
    {
        try {
            $response = $this->httpClient->post("/api/v2/collections/$collectionId/get", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return GetItemsResponse::from($result);
    }

    public function delete(string $collectionId, DeleteEmbeddingRequest $request): void
    {
        try {
            $this->httpClient->post("/api/v2/collections/$collectionId/delete", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }
    }

    public function count(string $collectionId): int
    {
        try {
            $response = $this->httpClient->get("/api/v2/collections/$collectionId/count");
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getNearestNeighbors(string $collectionId, QueryEmbeddingRequest $request): QueryItemsResponse
    {
        try {
            $response = $this->httpClient->post("/api/v2/collections/$collectionId/query", [
                'json' => $request->toArray()
            ]);
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        $result = json_decode($response->getBody()->getContents(), true);

        return QueryItemsResponse::from($result);
    }

    public function reset(): bool
    {
        try {
            $response = $this->httpClient->post('/api/v2/reset');
        } catch (ClientExceptionInterface $e) {
            $this->handleChromaApiException($e);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    private function handleChromaApiException(\Exception|ClientExceptionInterface $e): void
    {
        if ($e instanceof ConnectException) {
            $context = $e->getHandlerContext();
            $message = $context['error'] ?? $e->getMessage();
            $code = $context['errno'] ?? $e->getCode();
            throw new ChromaConnectionException($message, $code);
        }

        if ($e instanceof RequestException) {
            $context = $e->getHandlerContext();
            $code = $context['errno'] ?? $e->getCode();

            if ($code === 404) {
                throw new ChromaNotFoundException($e->getMessage(), $code);
            }

            $errorString = $e->getResponse()->getBody()->getContents();

            if (preg_match('/(?<={"\"error\"\:\")([^"]*)/', $errorString, $matches)) {
                $errorString = $matches[1];
            }

            $error = json_decode($errorString, true);

            if ($error !== null) {
                // If the structure is 'error' => 'NotFoundError("Collection not found")'
                if (preg_match(
                    '/^(?P<error_type>\w+)\((?P<message>.*)\)$/',
                    $error['error'] ?? '',
                    $matches
                )
                ) {
                    if (isset($matches['message'])) {
                        $error_type = $matches['error_type'] ?? 'UnknownError';
                        $message = $matches['message'];

                        // Remove trailing and leading quotes
                        if (str_starts_with($message, "'") && str_ends_with($message, "'")) {
                            $message = substr($message, 1, -1);
                        }

                        ChromaException::throwSpecific($message, $error_type, $e->getCode());
                    }
                }

                // If the structure is 'detail' => 'Collection not found'
                if (isset($error['detail'])) {
                    $message = $error['detail'];
                    $error_type = ChromaException::inferTypeFromMessage($message);


                    ChromaException::throwSpecific($message, $error_type, $e->getCode());
                }

                // If the structure is {'error': 'Error Type', 'message' : 'Error message'}
                if (isset($error['error']) && isset($error['message'])) {
                    ChromaException::throwSpecific($error['message'], $error['error'], $e->getCode());
                }

                // If the structure is 'error' => 'Collection not found'
                if (isset($error['error'])) {
                    $message = $error['error'];
                    $error_type = ChromaException::inferTypeFromMessage($message);

                    ChromaException::throwSpecific($message, $error_type, $e->getCode());
                }
            }
        }

        throw new ChromaException($e->getMessage(), $e->getCode());
    }
}
