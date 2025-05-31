<?php

namespace App\Services\RestrictionRuleHandlers;

use App\Services\Contracts\RestrictionRuleContract;
use App\Services\RestrictionRule;
use Filament\Forms\Components\Checkbox;

class CoppaRestricted extends RestrictionRule implements RestrictionRuleContract
{
    protected string $name = 'Forum is Coppa Restricted';

    public function isRestricted(): bool
    {
        return false;
    }
    public function makeRestrictionComponent($gateType)
    {
        return Checkbox::make('restrictions.' . $gateType . '.' . class_basename(__CLASS__))->label($this->name)->default(false);

    }

}
