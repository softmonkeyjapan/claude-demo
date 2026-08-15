<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

final class ContactPolicy
{
    public function view(User $user, Contact $contact): bool
    {
        return $user->id === $contact->user_id;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->id === $contact->user_id;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->id === $contact->user_id;
    }
}
