<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Unit Tests">
            <directory>./tests/Unit</directory>
        </testsuite>
    </testsuites>
</phpunit>

<?php

namespace Tests\Unit;

use Tests\TestCase;

class BasicTest extends TestCase
{
    public function testBasicAssertion()
    {
        $this->assertTrue(true);
    }
}