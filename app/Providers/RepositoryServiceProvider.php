<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\ContactRepository;
use App\Repositories\Contracts\ContactRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepositoryContract::class => UserRepository::class,
        ContactRepositoryContract::class => ContactRepository::class,
    ];
}
