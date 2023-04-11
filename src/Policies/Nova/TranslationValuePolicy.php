<?php

namespace CodebarAG\LaravelTranslations\Policies\Nova;

use App\Models\User;
use CodebarAG\LaravelTranslations\Models\TranslationValue;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TranslationValuePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, TranslationValue $translationValue): Response
    {
        return Response::allow();
    }

    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, TranslationValue $translationValue): Response
    {
        return Response::allow();
    }

    public function delete(User $user, TranslationValue $translationValue): Response
    {
        return Response::allow();
    }

    public function restore(User $user, TranslationValue $translationValue): Response
    {
        return Response::allow();
    }

    public function forceDelete(User $user, TranslationValue $translationValue): Response
    {
        return Response::deny();
    }
}
