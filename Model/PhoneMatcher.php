<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Model;

/**
 * Compares phone numbers the way Arasta does: digits only, a leading 00 or 0 and a country code of up to three digits
 * are ignored, and at least nine national digits must match. "07000 000000", "+44 7000 000000" and "0044 7000000000"
 * are the same line; short numbers never match.
 */
class PhoneMatcher
{
    public const MIN_DIGITS = 9;

    /** Digits without a leading 00 international prefix. */
    public function digits(string $phone): string
    {
        $digits = (string) preg_replace('/\D+/', '', $phone);
        return str_starts_with($digits, '00') ? substr($digits, 2) : $digits;
    }

    /** The last nine digits, used to narrow the database search; empty when the number is too short. */
    public function tail(string $phone): string
    {
        $digits = $this->digits($phone);
        return strlen($digits) >= self::MIN_DIGITS ? substr($digits, -self::MIN_DIGITS) : '';
    }

    public function matches(string $a, string $b): bool
    {
        $da = $this->digits($a);
        $db = $this->digits($b);
        if (strlen($da) < self::MIN_DIGITS || strlen($db) < self::MIN_DIGITS) {
            return false;
        }
        if ($da === $db) {
            return true;
        }
        $na = ltrim($da, '0');
        $nb = ltrim($db, '0');
        [$short, $long] = strlen($na) <= strlen($nb) ? [$na, $nb] : [$nb, $na];
        return strlen($short) >= self::MIN_DIGITS && str_ends_with($long, $short) && strlen($long) - strlen($short) <= 3;
    }
}
