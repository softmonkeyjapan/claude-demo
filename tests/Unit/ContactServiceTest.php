<?php

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Contracts\ContactRepositoryContract;
use App\Services\ContactService;

function contactServiceWithMockedRepository(): array
{
    $repository = Mockery::mock(ContactRepositoryContract::class);
    $service = new ContactService($repository);

    return [$service, $repository];
}

function ownerUser(): User
{
    $user = new User;
    $user->id = 1;

    return $user;
}

test('createContact leaves an empty emails list untouched', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['emails'] === [])
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [],
        'phones' => [],
    ]);
});

test('createContact leaves an empty phones list untouched', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['phones'] === [])
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [],
        'phones' => [],
    ]);
});

test('createContact marks the first email as primary when none is marked', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['emails'][0]['is_primary'] === true
            && $data['emails'][1]['is_primary'] === false)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [
            ['email' => 'a@example.com', 'label' => 'personal', 'is_primary' => false],
            ['email' => 'b@example.com', 'label' => 'work', 'is_primary' => false],
        ],
        'phones' => [],
    ]);
});

test('createContact marks the first phone as primary when none is marked', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['phones'][0]['is_primary'] === true
            && $data['phones'][1]['is_primary'] === false)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [],
        'phones' => [
            ['phone_number' => '0600000001', 'label' => 'personal', 'is_primary' => false],
            ['phone_number' => '0600000002', 'label' => 'work', 'is_primary' => false],
        ],
    ]);
});

test('createContact keeps the already-marked email as the only primary', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['emails'][0]['is_primary'] === false
            && $data['emails'][1]['is_primary'] === true)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [
            ['email' => 'a@example.com', 'label' => 'personal', 'is_primary' => false],
            ['email' => 'b@example.com', 'label' => 'work', 'is_primary' => true],
        ],
        'phones' => [],
    ]);
});

test('createContact keeps the already-marked phone as the only primary', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['phones'][0]['is_primary'] === false
            && $data['phones'][1]['is_primary'] === true)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [],
        'phones' => [
            ['phone_number' => '0600000001', 'label' => 'personal', 'is_primary' => false],
            ['phone_number' => '0600000002', 'label' => 'work', 'is_primary' => true],
        ],
    ]);
});

test('createContact keeps only the first marked email as primary when several are marked', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['emails'][0]['is_primary'] === true
            && $data['emails'][1]['is_primary'] === false
            && $data['emails'][2]['is_primary'] === false)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [
            ['email' => 'a@example.com', 'label' => 'personal', 'is_primary' => true],
            ['email' => 'b@example.com', 'label' => 'work', 'is_primary' => true],
            ['email' => 'c@example.com', 'label' => 'other', 'is_primary' => true],
        ],
        'phones' => [],
    ]);
});

test('createContact keeps only the first marked phone as primary when several are marked', function () {
    [$service, $repository] = contactServiceWithMockedRepository();

    $repository->shouldReceive('create')
        ->once()
        ->withArgs(fn (array $data) => $data['phones'][0]['is_primary'] === true
            && $data['phones'][1]['is_primary'] === false
            && $data['phones'][2]['is_primary'] === false)
        ->andReturn(new Contact);

    $service->createContact(ownerUser(), [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [],
        'phones' => [
            ['phone_number' => '0600000001', 'label' => 'personal', 'is_primary' => true],
            ['phone_number' => '0600000002', 'label' => 'work', 'is_primary' => true],
            ['phone_number' => '0600000003', 'label' => 'other', 'is_primary' => true],
        ],
    ]);
});

test('updateContact applies the same normalization as createContact', function () {
    [$service, $repository] = contactServiceWithMockedRepository();
    $contact = new Contact;

    $repository->shouldReceive('update')
        ->once()
        ->withArgs(fn (Contact $target, array $data) => $target === $contact
            && $data['emails'][0]['is_primary'] === true)
        ->andReturn($contact);

    $service->updateContact($contact, [
        'first_name' => 'Jane',
        'last_name' => null,
        'emails' => [
            ['email' => 'a@example.com', 'label' => 'personal', 'is_primary' => false],
        ],
        'phones' => [],
    ]);
});
