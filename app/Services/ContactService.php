<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Contracts\ContactRepositoryContract;

final class ContactService
{
    public function __construct(
        private readonly ContactRepositoryContract $contacts,
    ) {}

    /**
     * @param  array{first_name: ?string, last_name: ?string, emails: array<int, array<string, mixed>>, phones: array<int, array<string, mixed>>}  $data
     */
    public function createContact(User $user, array $data): Contact
    {
        return $this->contacts->create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'emails' => $this->normalizePrimary($data['emails']),
            'phones' => $this->normalizePrimary($data['phones']),
        ]);
    }

    /**
     * @param  array{first_name: ?string, last_name: ?string, emails: array<int, array<string, mixed>>, phones: array<int, array<string, mixed>>}  $data
     */
    public function updateContact(Contact $contact, array $data): Contact
    {
        return $this->contacts->update($contact, [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'emails' => $this->normalizePrimary($data['emails']),
            'phones' => $this->normalizePrimary($data['phones']),
        ]);
    }

    public function deleteContact(Contact $contact): void
    {
        $this->contacts->delete($contact);
    }

    /**
     * Ensures exactly one item is marked primary when the list is non-empty,
     * respecting the order the items were received in.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function normalizePrimary(array $items): array
    {
        if ($items === []) {
            return $items;
        }

        $hasPrimary = false;

        foreach ($items as $item) {
            if ($item['is_primary'] === true) {
                $hasPrimary = true;
                break;
            }
        }

        if (! $hasPrimary) {
            $firstKey = array_key_first($items);
            $items[$firstKey]['is_primary'] = true;

            return $items;
        }

        $firstPrimarySeen = false;

        foreach ($items as $key => $item) {
            if ($item['is_primary'] !== true) {
                continue;
            }

            if ($firstPrimarySeen) {
                $items[$key]['is_primary'] = false;
            } else {
                $firstPrimarySeen = true;
            }
        }

        return $items;
    }
}
