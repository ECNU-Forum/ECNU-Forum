<?php

namespace FoF\Masquerade\FieldType;

class EmailField extends BaseField
{
    public function overrideAttributes(): array
    {
        return [
            'validation' => 'email',
        ];
    }
}
