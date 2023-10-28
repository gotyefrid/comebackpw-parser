<?php

namespace unit\Services;

use Codeception\Test\Unit;
use Gotyefrid\ComebackpwParser\Services\ParserService;

class ParserTest extends Unit
{

    public function testTrr()
    {
        static::assertSame(123, ParserService::foo());
    }
}