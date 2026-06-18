<?php

namespace App\Enums;

enum CompanyName: string
{
    case COMPANY_NAME = 'Niketan Society';

    // Optional: label method if you want display-friendly name
    public function label(): string
    {
        return match($this) {
            self::COMPANY_NAME => 'Niketan Society',
        };
    }
}
