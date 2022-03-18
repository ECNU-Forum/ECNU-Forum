<?php

namespace FoF\Masquerade\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;

class FieldSerializer extends AbstractSerializer
{
    protected function getDefaultAttributes($model): array
    {
        return $model->toArray();
    }

    public function getType($model): string
    {
        return 'masquerade-field';
    }

    public function answer($model): ?Relationship
    {
        if (!$this->getActor()) {
            return null;
        }

        $for = $model->for ?: $this->getActor()->id;

        if ($answer = $model->answers()->where('user_id', $for)->first()) {
            return new Relationship(new Resource(
                $answer,
                new AnswerSerializer
            ));
        } else {
            return null;
        }
    }
}
