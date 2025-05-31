<?php

namespace App\Services\Traits;

use App\Models\ModelHasRestriction;
use App\Services\RestrictionRule;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\stringStartsWith;

trait Restrictable
{
    public function save(array $options = [])
    {
        $this->restrictable()->delete();
        $restrictions = $this->restrictions;
        unset($this->restrictions);
        $model = parent::save($options);
        if (!$restrictions) {
            return;
        }
        foreach ($restrictions as $gate => $restriction) {
            $this->processRestrictionString($model, $gate, $restriction);
        }
    }

    public function restrictable(): MorphMany
    {
        return $this->morphMany(ModelHasRestriction::class, 'restrictable');
    }

    public static function enumerateRestrictionRules()
    {
        $rules = Cache::remember('restriction_handlers', 10, function () {
        $rules = [];
        $namespace = "App\Services\RestrictionRuleHandlers\\";
        $files = Storage::disk('rules')->allFiles();
        foreach ($files as $file) {
            $class = $namespace . str_replace('.php', '', $file);
            $r = new $class;
            $rules[$class] = ['name' => $r->getName(), 'type' => $r->getType()];
        }
        return $rules;
        });
        return $rules;
    }

    public static function getRestrictableGateTypes()
    {
        return [
            'list', 'create', 'read', 'update', 'delete',
        ];
    }

    protected function processRestrictionString($parent, $gate, $restrictions)
    {
        foreach ($restrictions as $restrictionClass => $values) {
            $restriction = new ModelHasRestriction();
            $restriction->restriction = $restrictionClass;
            $restriction->restriction_gate_method = $gate;
            $restriction->restriction_values = json_encode($values);
            $this->restrictable()->save($restriction);
        }
    }
    protected static function buildRestrictionSchema(): array
    {
        $gateTypes = self::getRestrictableGateTypes();
        $restrictionHandlers = static::getModel()::enumerateRestrictionRules();
        foreach ($restrictionHandlers as $restrictionClass => $restrictionHandler) {
            $component = (new $restrictionClass);
            $handlers[] = $component;
        }
        foreach ($gateTypes as $gateType) {
            $components = [];
            foreach ($handlers as $handler) {
                $components[] = $handler->makeRestrictionComponent($gateType);
            }
            $schema[] = Fieldset::make($gateType)
                ->schema($components);
        }
        return $schema;
        $rules = [];
        return $rules;
    }

    public function delete()
    {
        $this->restrictable()->delete();
        return parent::delete();
    }

    protected static function bootRestrictableTrait()
    {
        self::deleting(function ($model) {
            $model->restrictable->delete();
        });
    }
}
