<?php

namespace App\Services;

use App\Domains\Contacts\Models\Contacts;
use App\Domains\Contacts\Repositories\ContactsRepository;
use App\Http\CacheableServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ContactsService implements CacheableServiceInterface
{
    private ContactsRepository $contactsRepository;
    private CacheService $cacheService;
    private const CACHE_ENTITY_PREFIX = 'contacts_entity';

    /**
     * @param ContactsRepository $contactsRepository
     * @param CacheService $cacheService
     */
    public function __construct(ContactsRepository $contactsRepository, CacheService $cacheService)
    {
        $this->contactsRepository = $contactsRepository;
        $this->cacheService = $cacheService;
    }

    public function updateContacts(array $only, string $id): void
    {
        $curr = Contacts::findOrFail($id);
        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, 0);

        $curr->fill($only);
        $curr->save();
    }

    public function addContacts(array $only): Contacts
    {
        $contacts =  $this->contactsRepository->create($only);

        $this->clearEntityCache(self::CACHE_ENTITY_PREFIX, 0);

        return $contacts;
    }

    public function getListWithCache(?int $categoryId): Collection
    {
        return $this->cacheService->rememberByCategory(
            self::CACHE_ENTITY_PREFIX,
            null,
            10, function () {
                return $this->contactsRepository->get();
        });
    }

    public function clearCache(mixed $categoryId, string $prefix): void
    {
        $this->cacheService->clearByCategory($prefix, $categoryId);
    }

        public function clearEntityCache(string $prefix, int $id): Void
    {
        $this->cacheService->clearEntity($prefix, $id);
    }

    public function getEntityWithCache(int $id): mixed
    {
        return $this->cacheService->rememberById(self::CACHE_ENTITY_PREFIX, 0, 10, function () use ($id) {
            return $this->contactsRepository->get();
        });
    }
}
