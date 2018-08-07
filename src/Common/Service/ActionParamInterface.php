<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Service;

use Soluble\MediaTools\Video\Exception\UnsetParamException;

interface ActionParamInterface
{
    /**
     * Return the internal array holding params.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Test whether a param is built-in or valid.
     */
    public function isParamValid(string $paramName): bool;

    /**
     * @return mixed
     *
     * @throws UnsetParamException
     */
    public function getParam(string $paramName);

    public function hasParam(string $paramName): bool;
}
