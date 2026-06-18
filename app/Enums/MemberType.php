<?php

namespace App\Enums;

enum MemberType: string
{
    case LIFE = 'life';
    case DONATE = 'donate';

    public function label(): string
    {
        return match($this) {
            self::LIFE => 'Life',
            self::DONATE => 'Donate',
        };
    }
}