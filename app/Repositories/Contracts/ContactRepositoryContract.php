<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Support\Collection;

interface ContactRepositoryContract
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Contact;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Contact $contact, array $data): Contact;

    public function delete(Contact $contact): void;

    /**
     * @return Collection<int, Contact>
     */
    public function search(User $user, string $query): Collection;
}
