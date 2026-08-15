<?php

use App\Enums\ContactLabel;
use App\Models\Contact;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Models\User;

test('deleting a contact cascades to its emails and phones', function () {
    $contact = Contact::factory()->create();
    $email = $contact->emails()->create([
        'email' => 'jane@example.com',
        'label' => ContactLabel::Personal,
        'is_primary' => true,
    ]);
    $phone = $contact->phones()->create([
        'phone_number' => '0600000000',
        'label' => ContactLabel::Personal,
        'is_primary' => true,
    ]);

    $contact->delete();

    expect(ContactEmail::find($email->id))->toBeNull();
    expect(ContactPhone::find($phone->id))->toBeNull();
});

test('the owner can view, update, and delete their contact', function () {
    $owner = User::factory()->create();
    $contact = Contact::factory()->for($owner)->create();

    expect($owner->can('view', $contact))->toBeTrue();
    expect($owner->can('update', $contact))->toBeTrue();
    expect($owner->can('delete', $contact))->toBeTrue();
});

test('another user cannot view, update, or delete someone else\'s contact', function () {
    $owner = User::factory()->create();
    $contact = Contact::factory()->for($owner)->create();
    $stranger = User::factory()->create();

    expect($stranger->can('view', $contact))->toBeFalse();
    expect($stranger->can('update', $contact))->toBeFalse();
    expect($stranger->can('delete', $contact))->toBeFalse();
});
