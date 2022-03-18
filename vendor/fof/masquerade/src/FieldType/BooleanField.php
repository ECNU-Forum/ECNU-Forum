<?php

namespace FoF\Masquerade\FieldType;

class BooleanField extends BaseField
{
    public function overrideAttributes(): array
    {
        return [
            'validation' => 'boolean',
        ];
    }
}
