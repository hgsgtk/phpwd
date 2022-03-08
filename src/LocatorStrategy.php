<?php

declare(strict_types=1);

namespace Phpwd;

enum LocatorStrategy: string
{
    case Css = 'css selector';
    case LinkText = 'link text';
    case PartialLinkText = 'partial link text';
    case TagName = 'tag name';
    case Xpath = 'xpath';
}
