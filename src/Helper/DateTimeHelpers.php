<?php

namespace App\Helper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class DateTimeHelpers
{
    public static function createClassic(?string $datetime = null): DateTimeInterface
    {
        try {
            return new DateTime($datetime ?: 'now', new DateTimeZone('Europe/Paris'));
        } catch (Exception $e) {
            return new DateTime('now');
        }
    }

    public static function createImmutable(?string $datetime = null): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($datetime ?: 'now', new DateTimeZone('Europe/Paris'));
        } catch (Exception $e) {
            return new DateTimeImmutable('now');
        }
    }
}
