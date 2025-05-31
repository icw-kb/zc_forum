<?php

namespace App\Services\RestrictionRuleHandlers;

use App\Services\Contracts\RestrictionRuleContract;
use App\Services\RestrictionRule;
use Filament\Forms\Components\Checkbox;

class IsRegistered extends RestrictionRule implements RestrictionRuleContract
{
    protected string $name = 'User is Registered';

    public function isRestricted(): bool
    {
        if (auth()->check()) {
            return false;
        }
        return true;
    }

    public function makeRestrictionComponent($gateType)
    {
        return Checkbox::make('restrictions.' . $gateType . '.' . class_basename(__CLASS__))->label($this->name);
    }

}
