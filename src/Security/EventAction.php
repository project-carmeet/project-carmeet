<?php

declare(strict_types=1);

namespace App\Security;

final class EventAction
{
    public const VIEW = 'view_event_action';
    public const CREATE = 'create_event_action';
    public const EDIT = 'edit_event_action';
    public const DELETE = 'delete_event_action';

    public static function isAction(string $action): bool
    {
        return in_array($action, self::getActions(), true);
    }

    /**
     * @return array<string>
     */
    public static function getActions(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }
}
