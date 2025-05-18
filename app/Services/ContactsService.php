<?php

namespace App\Services;

use App\Models\Contacts;
use App\Repositories\ContactsRepository;

class ContactsService
{
    private ContactsRepository $contactsRepository;

    /**
     * @param ContactsRepository $contactsRepository
     */
    public function __construct(ContactsRepository $contactsRepository)
    {
        $this->contactsRepository = $contactsRepository;
    }

    public function getContacts(): Contacts
    {
        return $this->contactsRepository->get();
    }

    public function updateContacts(array $only, string $id): void
    {
        $curr = Contacts::findOrFail($id);
        $curr->fill($only);
        $curr->save();
    }

    public function addContacts(array $only): Contacts
    {
        return $this->contactsRepository->create($only);
    }
}
