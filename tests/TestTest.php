<?php
use PHPUnit\Framework\TestCase;

final class TestTest extends TestCase 
{
    public function testPass(): void
    {
        $stack = [];
        $this->assertSame(0, count($stack));

        $stack[] = "foo";
        $this->assertSame(1, count($stack));
    }
}
