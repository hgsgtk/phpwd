<?php

declare(strict_types=1);

namespace Phpwd;

final class ElementId
{
    public function __construct(public string $id)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
