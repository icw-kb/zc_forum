<?php

namespace App\Services;

use Illuminate\Database\Eloquent\MissingAttributeException;

abstract class RestrictionRule
{
    protected string $name = '';
    protected string $type = 'Checkbox';
    const TYPES = ['Checkbox', 'Select'];

    public function getName()
    {
        if (empty($this->name)) {
            throw new \Exception('Missing $name property');
        }
        return $this->name;
    }
    public function getType()
    {
        if (empty($this->type)) {
            throw new \Exception('Missing $type property');
        }
        return $this->type;
    }
}
