<?php
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Test\Unit;

use PHPUnit\Framework\TestCase;
use CommerceGears\ArastaLiveChat\Model\PhoneMatcher;

class PhoneMatcherTest extends TestCase
{
    /** the same line written in local, international and 00 form matches */
    public function testSameLineMatches(): void
    {
        $m = new PhoneMatcher();
        self::assertTrue($m->matches('07000 000000', '+44 7000 000000'));
        self::assertTrue($m->matches('0044 7000000000', '+447000000000'));
        self::assertTrue($m->matches('0532 123 45 67', '+90 532 123 45 67'));
        self::assertTrue($m->matches('+447000000000', '+447000000000'));
    }

    /** another number, a short number or an unrelated long number never matches */
    public function testDifferentOrShortNumbersDoNotMatch(): void
    {
        $m = new PhoneMatcher();
        self::assertFalse($m->matches('07000000000', '+447000000001'));
        self::assertFalse($m->matches('1234', '+441234'));
        self::assertFalse($m->matches('000000000', '+447000000000000'));
        self::assertFalse($m->matches('', '+447000000000'));
    }

    /** the search tail is the last nine digits, empty for a short number */
    public function testTail(): void
    {
        $m = new PhoneMatcher();
        self::assertSame('000000000', $m->tail('+44 7000 000000'));
        self::assertSame('', $m->tail('12345678'));
    }
}
