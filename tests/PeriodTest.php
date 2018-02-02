<?php

declare(strict_types=1);

namespace MailExport;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \MailExport\Period
 */
class PeriodTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCreationWithDefaultValues()
    {
        $period = new Period();
        $this->assertEquals(0, $period->from());
        $this->assertEquals(0, $period->to());

        $period = new Period(10);
        $this->assertEquals(10, $period->from());
        $this->assertEquals(10, $period->to());

        $period = new Period(null, 10);
        $this->assertEquals(0, $period->from());
        $this->assertEquals(10, $period->to());
    }

    /**
     * @covers ::__construct
     */
    public function testWillThrowExceptionIfInputValuesAreInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp period from -1 to -1');
        new Period(-1, -1);
    }

    /**
     * @covers ::__construct
     */
    public function testWillThrowExceptionIfInputValuesIsInvalidPeriod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp period from 123 to 0');
        new Period(123, 0);
    }

    /**
     * @covers ::from
     * @covers ::to
     */
    public function testFrom()
    {
        $period = new Period(123, 321);
        $this->assertEquals(123, $period->from());
        $this->assertEquals(321, $period->to());
    }

    /**
     * @covers ::contains
     */
    public function testContains()
    {
        $period = new Period(10, 20);
        $this->assertFalse($period->contains(5));
        $this->assertTrue($period->contains(15));
    }
}
