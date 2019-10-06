<?php

namespace Ollieread\Core\Services;

use Illuminate\Validation\ValidationException;
use Ollieread\Core\Models\Redirect;
use Ollieread\Core\Operations\CreateRedirect;
use Ollieread\Core\Operations\DeleteRedirect;
use Ollieread\Core\Operations\GetRedirect;
use Ollieread\Core\Operations\UpdateRedirect;

class Redirects
{
    public static function create(string $from, string $to): void
    {
        if (! self::alreadyPresent($from, $to)) {
            self::updateExisting($from, $to);
            self::createNew($from, $to);
        }
    }

    public static function alreadyPresent(string $from, string $to): bool
    {
        return (new GetRedirect)->setFrom($from)->perform() !== null;
    }

    public static function updateExisting(string $from, string $to): bool
    {
        try {
            return (new UpdateRedirect)
                ->setRedirect((new GetRedirect)
                    ->setTo($from)
                    ->perform())
                ->setInput(compact('to'))
                ->perform();
        } catch (ValidationException $exception) {
            report($exception);
        }

        return false;
    }

    public static function createNew(string $from, string $to): ?Redirect
    {
        try {
            return (new CreateRedirect)->setInput(compact('from', 'to'))->perform();
        } catch (ValidationException $exception) {
            report($exception);
        }

        return null;
    }

    public static function getRedirect(string $uri): ?Redirect
    {
        return (new GetRedirect)->setFrom($uri)->perform();
    }

    public static function delete(string $uri): bool
    {
        return (new DeleteRedirect)->setUri($uri)->perform();
    }
}
