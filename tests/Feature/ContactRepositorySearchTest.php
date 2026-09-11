<?php

use App\Enums\ContactLabel;
use App\Models\Contact;
use App\Models\User;
use App\Repositories\Contracts\ContactRepositoryContract;

test('search matches a first name regardless of case', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->for($user)->create(['first_name' => 'Jane', 'last_name' => 'Doe']);

    $results = app(ContactRepositoryContract::class)->search($user, 'JANE');

    expect($results->pluck('id'))->toContain($contact->id);
});

test('search matches an email regardless of case', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->for($user)->create();
    $contact->emails()->create([
        'email' => 'Jane.Doe@Example.com',
        'label' => ContactLabel::Personal,
        'is_primary' => true,
    ]);

    $results = app(ContactRepositoryContract::class)->search($user, 'jane.doe@example.com');

    expect($results->pluck('id'))->toContain($contact->id);
});

test('search never returns another user\'s contacts', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    Contact::factory()->for($owner)->create(['first_name' => 'Jane']);

    $results = app(ContactRepositoryContract::class)->search($stranger, 'jane');

    expect($results)->toBeEmpty();
});
