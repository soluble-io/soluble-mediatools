<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Common\Service;

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
     * @param mixed $defaultValue if param does not exists set this one
     *
     * @return mixed
     */
    public function getParam(string $paramName, $defaultValue = null);

    public function hasParam(string $paramName): bool;
}
