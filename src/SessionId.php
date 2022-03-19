<?php

declare(strict_types=1);

namespace Phpwd;

final class SessionId
{
    /**
     * @var string sessionId
     * @link https://www.w3.org/TR/webdriver/#new-session
     */
    public function __construct(private string $id)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
