<?php

declare(strict_types=1);

namespace Phpwd;

interface ClientInterface 
{
    /**
     * send GET request.
     *
     * @param string $path
     * @return array<string,mixed>
     */
    public function get(string $path): array;

    /**
     * send POST request.
     *
     * @param string $path
     * @param array $body
     * @return array<string,mixed>
     */
    public function post(string $path, array $body): array;

    /**
     * send DELETE request.
     *
     * @return array<string,mixed>
     */
    public function delete(string $path): array;
}
