<?php

namespace App\Services\RestrictionRuleHandlers;

use App\Services\Contracts\RestrictionRuleContract;
use App\Services\Contracts\RestrictionRuleWithValuesContract;
use App\Services\RestrictionRule;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;

class BelongsToUserGroup extends RestrictionRule implements RestrictionRuleContract
{
    protected string $name = 'User belongs to user group(s)';

    public function isRestricted(): bool
    {
        return false;
    }

    public function makeRestrictionComponent($gateType)
    {
        $roles = Role::all()->pluck('name', 'name')->toArray();
        return Select::make('restrictions.' . $gateType . '.' . class_basename(__CLASS__))->label($this->name)->options($roles)->multiple()->default(['super_admin']);
    }
}
