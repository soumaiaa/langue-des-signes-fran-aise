<?php
// src/Service/CookieManager.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;


class CookieManager
{
    private const COOKIE_NAME = 'my_cookie';

    public function __construct()
    {
        // Vous pouvez initialiser des propriétés ici si nécessaire
    }

    public function setCookie(string $value, int $expire = 0): void
    {
        setcookie(self::COOKIE_NAME, $value, $expire, '/', '', false, true); // Secure et HttpOnly
    }

    public function getCookie(Request $request): ?string
    {
        return $request->cookies->get(self::COOKIE_NAME);
    }

    public function deleteCookie(): void
    {
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/', '', false, true);
    }

    public function hasConsented(Request $request): bool
    {
        return $request->cookies->has(self::COOKIE_NAME);
    }
}
