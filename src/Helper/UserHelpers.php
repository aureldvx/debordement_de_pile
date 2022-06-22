<?php

namespace App\Helper;

use App\Entity\User;

class UserHelpers
{
    public static function displayPseudo(User $user): string
    {
        $restrictedUser = !$user->isEnabled() || null !== $user->getBlockedAt();

        if ($restrictedUser) {
            return 'Utilisateur anonyme';
        }

        return $user->getPseudo();
    }

    public static function getIp(): string
    {
        $defaultIp = '127.0.0.1';

        $activeHeaders = [];

        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_PRAGMA',
            'HTTP_XONNECTION',
            'HTTP_CACHE_INFO',
            'HTTP_XPROXY',
            'HTTP_PROXY',
            'HTTP_PROXY_CONNECTION',
            'HTTP_VIA',
            'HTTP_X_COMING_FROM',
            'HTTP_COMING_FROM',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'ZHTTP_CACHE_CONTROL',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $key) {
            if (array_key_exists($key, $_SERVER)) {
                $activeHeaders[$key] = $_SERVER[$key];
            }
        }

        if (count($activeHeaders) > 1) {
            unset($activeHeaders['REMOTE_ADDR']);
        }

        $realIP = $activeHeaders[array_rand($activeHeaders)];

        if (filter_var($realIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $realIP;
        }

        return $defaultIp;
    }
}
