<?php

declare(strict_types=1);

namespace Phpwd;

use JetBrains\PhpStorm\Pure;

/**
 * @method static self css()
 * @method static self linkText()
 * @method static self partialLinkText()
 * @method static self tagName()
 * @method static self xpath()
 *
 * @link https://www.w3.org/TR/webdriver/#locator-strategies
 */
final class LocatorStrategy
{
    use Enum;

    #[Pure]
    public function toString(): string
    {
        return (string)$this->getValue();
    }

    protected static function enum(): array
    {
        return [
            'css' => 'css selector',
            'linkText' => 'link text',
            'partialLinkText' => 'partial link text',
            'tagName' => 'tag name',
            'xpath' => 'xpath',
        ];
    }
}
