<?php

namespace App\Repositories;

use App\Models\Contacts;

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
