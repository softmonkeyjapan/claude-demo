<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Contracts\ContactRepositoryContract;
use Illuminate\Support\Collection;

final class ContactRepository implements ContactRepositoryContract
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Contact
    {
        $contact = Contact::create([
            'user_id' => $data['user_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        $this->syncEmailsAndPhones($contact, $data);

        return $contact->load(['emails', 'phones']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Contact $contact, array $data): Contact
    {
        $contact->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);
        $contact->save();

        $contact->emails()->delete();
        $contact->phones()->delete();

        $this->syncEmailsAndPhones($contact, $data);

        return $contact->load(['emails', 'phones']);
    }

    public function delete(Contact $contact): void
    {
        $contact->delete();
    }

    /**
     * @return Collection<int, Contact>
     */
    public function search(User $user, string $query): Collection
    {
        $needle = '%'.mb_strtolower($query).'%';

        return Contact::query()
            ->where('user_id', $user->id)
            ->where(function ($builder) use ($needle) {
                $builder
                    ->whereRaw('LOWER(first_name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$needle])
                    ->orWhereHas('emails', function ($emails) use ($needle) {
                        $emails->whereRaw('LOWER(email) LIKE ?', [$needle]);
                    })
                    ->orWhereHas('phones', function ($phones) use ($needle) {
                        $phones->whereRaw('LOWER(phone_number) LIKE ?', [$needle]);
                    });
            })
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncEmailsAndPhones(Contact $contact, array $data): void
    {
        foreach ($data['emails'] as $email) {
            $contact->emails()->create($email);
        }

        foreach ($data['phones'] as $phone) {
            $contact->phones()->create($phone);
        }
    }
}
