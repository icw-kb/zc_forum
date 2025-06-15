<?php

namespace App\Services\Contracts;

interface RestrictionRuleContract
{
    public function isRestricted(): bool;

    public function makeRestrictionComponent(string $gateType);
}
