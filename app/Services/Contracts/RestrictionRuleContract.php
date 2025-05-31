<?php

namespace App\Services\Contracts;

use Filament\Forms\Components\Select;

interface RestrictionRuleContract
{
    public function isRestricted(): bool;
    public function makeRestrictionComponent(string $gateType);
}
