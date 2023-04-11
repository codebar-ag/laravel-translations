<?php

namespace CodebarAg\LaravelTranslations\Policies\Nova;

use App\Models\User;
use CodebarAg\LaravelTranslations\Models\Translation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TranslationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Translation $translation): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return Response::deny();
    }

    public function update(User $user, Translation $translation): Response
    {
        return Response::deny();
    }

    public function delete(User $user, Translation $translation): Response
    {
        return Response::deny();
    }

    public function restore(User $user, Translation $translation): Response
    {
        return Response::deny();
    }

    public function forceDelete(User $user, Translation $translation): Response
    {
        return Response::deny();
    }
}
