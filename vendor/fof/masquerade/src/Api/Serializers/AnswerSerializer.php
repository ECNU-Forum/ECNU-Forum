<?php

namespace FoF\Masquerade\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Tobscure\JsonApi\Relationship;

class AnswerSerializer extends AbstractSerializer
{
    /**
     * @param \FoF\Masquerade\Answer $model
     */
    protected function getDefaultAttributes($model): array
    {
        return array_merge($model->toArray(), [
            'fieldId' => $model->field_id
        ]);
    }

    public function getType($model): string
    {
        return 'masquerade-answer';
    }

    public function field($model): ?Relationship
    {
        return $this->hasOne(
            $model->field,
            FieldSerializer::class
        );
    }
}
