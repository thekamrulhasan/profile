<?php

namespace App\Providers;

use App\Models\Experience;
use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use App\Policies\ExperiencePolicy;
use App\Policies\RolePolicy;
use App\Policies\SkillPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Experience::class => ExperiencePolicy::class,
        Skill::class => SkillPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
