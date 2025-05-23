<?php

namespace App\Domains\Contacts\Repositories;

use App\Domains\Contacts\Models\Contacts;

class ContactsRepository
{
    public function __construct()
    {
    }

    public function get(): Contacts
    {
        return Contacts::firstOrFail();
    }

    public function create(array $only): Contacts
    {
        return Contacts::create($only);
    }
}
