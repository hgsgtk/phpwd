<?php

declare(strict_types=1);

namespace Phpwd\Tests\Unit;

use Phpwd\Webdriver;

final class WebdriverTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct(): void
    {
        $sut = new Webdriver();

        $this->assertTrue(true, "initialize project");
    }
}