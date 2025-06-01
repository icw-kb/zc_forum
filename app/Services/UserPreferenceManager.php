<?php
// app/Services/UserPreferenceManager.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class UserPreferenceManager
{
    protected array $preferenceDefinitions;

    public function __construct()
    {
        $this->preferenceDefinitions = config('preferences', []);
    }

    public function getDefinitions(): array
    {
        // Sort by order if specified
        $definitions = $this->preferenceDefinitions;
        uasort($definitions, fn($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));
        
        // Sort fields within each group by order
        foreach ($definitions as &$group) {
            if (isset($group['fields'])) {
                uasort($group['fields'], fn($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));
            }
        }
        
        return $definitions;
    }

    public function getGroupDefinition(string $group): ?array
    {
        return $this->preferenceDefinitions[$group] ?? null;
    }

    public function getFieldDefinition(string $group, string $field): ?array
    {
        return $this->preferenceDefinitions[$group]['fields'][$field] ?? null;
    }

    public function getDefaultPreferences(): array
    {
        $defaults = [];
        foreach ($this->preferenceDefinitions as $group => $groupData) {
            if (isset($groupData['fields'])) {
                foreach ($groupData['fields'] as $field => $fieldData) {
                    $defaults[$group][$field] = $fieldData['default'] ?? null;
                }
            }
        }
        return $defaults;
    }

    public function get(User $user, string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember("user.preferences.{$user->id}", 86400, fn () => $user->preferences ?? []);
        return Arr::get($cached, $key, Arr::get($this->getDefaultPreferences(), $key, $default));
    }

    public function all(User $user): array
    {
        $cached = Cache::remember("user.preferences.{$user->id}", 86400, fn () => $user->preferences ?? []);
        return array_replace_recursive($this->getDefaultPreferences(), $cached);
    }

    public function set(User $user, array $preferences): void
    {
        // Validate preferences against definitions
        $validated = $this->validatePreferences($preferences);
        
        $current = $user->preferences ?? [];
        $merged = array_replace_recursive($current, $validated);
        $user->preferences = $merged;
        $user->save();

        Cache::put("user.preferences.{$user->id}", $merged, 86400);
    }

    public function reset(User $user, ?string $group = null): void
    {
        if ($group) {
            // Reset specific group to defaults
            $current = $user->preferences ?? [];
            $defaults = $this->getDefaultPreferences();
            $current[$group] = $defaults[$group] ?? [];
            $user->preferences = $current;
        } else {
            // Reset all preferences to defaults
            $user->preferences = $this->getDefaultPreferences();
        }
        
        $user->save();
        Cache::forget("user.preferences.{$user->id}");
    }

    public function types(): array
    {
        $types = [];
        foreach ($this->preferenceDefinitions as $group => $groupData) {
            if (isset($groupData['fields'])) {
                foreach ($groupData['fields'] as $field => $fieldData) {
                    if ($fieldData['type'] === 'select' && isset($fieldData['options'])) {
                        $options = implode(',', array_keys($fieldData['options']));
                        $types[$group][$field] = "select:{$options}";
                    } else {
                        $types[$group][$field] = $fieldData['type'] ?? 'string';
                    }
                }
            }
        }
        return $types;
    }

    protected function validatePreferences(array $preferences): array
    {
        $validated = [];
        
        foreach ($preferences as $group => $fields) {
            if (!isset($this->preferenceDefinitions[$group])) {
                continue;
            }
            
            if (!is_array($fields)) {
                continue;
            }
            
            foreach ($fields as $field => $value) {
                $fieldDef = $this->preferenceDefinitions[$group]['fields'][$field] ?? null;
                if (!$fieldDef) {
                    continue;
                }
                
                $validated[$group][$field] = $this->validateFieldValue($value, $fieldDef);
            }
        }
        
        return $validated;
    }

    protected function validateFieldValue(mixed $value, array $fieldDefinition): mixed
    {
        switch ($fieldDefinition['type']) {
            case 'boolean':
                return (bool) $value;
            
            case 'select':
                if (!isset($fieldDefinition['options'])) {
                    return $fieldDefinition['default'] ?? null;
                }
                $validOptions = array_keys($fieldDefinition['options']);
                return in_array($value, $validOptions, true) ? $value : ($fieldDefinition['default'] ?? null);
            
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : ($fieldDefinition['default'] ?? 0);
            
            case 'float':
                return is_numeric($value) ? (float) $value : ($fieldDefinition['default'] ?? 0.0);
            
            case 'string':
            case 'text':
                return (string) $value;
            
            default:
                return $value;
        }
    }

    public function getIconSvg(string $iconName): string
    {
        $icons = [
            'bell' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'cog' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
            'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
            'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>',
        ];
        
        return $icons[$iconName] ?? $icons['cog'];
    }

    /**
     * Get available groups for admin interface
     */
    public function getAvailableGroups(): array
    {
        return array_keys($this->preferenceDefinitions);
    }

    /**
     * Validate that a preference configuration is valid
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        
        foreach ($this->preferenceDefinitions as $groupKey => $group) {
            if (!isset($group['title'])) {
                $errors[] = "Group '{$groupKey}' is missing 'title'";
            }
            
            if (!isset($group['fields']) || !is_array($group['fields'])) {
                $errors[] = "Group '{$groupKey}' is missing 'fields' array";
                continue;
            }
            
            foreach ($group['fields'] as $fieldKey => $field) {
                $fieldPath = "{$groupKey}.{$fieldKey}";
                
                if (!isset($field['type'])) {
                    $errors[] = "Field '{$fieldPath}' is missing 'type'";
                }
                
                if (!isset($field['label'])) {
                    $errors[] = "Field '{$fieldPath}' is missing 'label'";
                }
                
                if (!isset($field['default'])) {
                    $errors[] = "Field '{$fieldPath}' is missing 'default' value";
                }
                
                if ($field['type'] === 'select' && !isset($field['options'])) {
                    $errors[] = "Field '{$fieldPath}' of type 'select' is missing 'options'";
                }
            }
        }
        
        return $errors;
    }
}