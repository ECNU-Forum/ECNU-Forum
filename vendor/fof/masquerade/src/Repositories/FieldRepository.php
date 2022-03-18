<?php

namespace FoF\Masquerade\Repositories;

use FoF\Masquerade\Answer;
use FoF\Masquerade\Field;
use FoF\Masquerade\FieldType\TypeFactory;
use Flarum\User\User;
use Illuminate\Cache\Repository;

class FieldRepository
{
    const CACHE_KEY_ALL_FIELDS = 'fof.masquerade.fields.all';
    const CACHE_KEY_UNCOMPLETED = 'fof.masquerade.uncompleted.u.%d';

    /**
     * @var Field
     */
    protected $field;
    /**
     * @var Repository
     */
    protected $cache;

    /**
     * FieldRepository constructor.
     * @param Field $field
     * @param Repository $cache
     */
    public function __construct(Field $field, Repository $cache)
    {
        $this->field = $field;
        $this->cache = $cache;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Field[]
     */
    public function all()
    {
        return $this->cache->rememberForever(static::CACHE_KEY_ALL_FIELDS, function () {
            return $this->query()->get();
        });
    }

    public function store(array $attributes): Field
    {
        $field = $this->field->newInstance();
        $field->sort = $this->highestSort();

        $type = TypeFactory::typeForField($attributes);

        $attributes = array_merge($attributes, $type->overrideAttributes());

        $field->fill($attributes);
        $field->save();

        $this->cache->forget(static::CACHE_KEY_ALL_FIELDS);

        return $field;
    }

    public function update($id, array $attributes): Field
    {
        /** @var Field */
        $field = $this->field->findOrFail($id);

        $type = TypeFactory::typeForField($attributes);

        $attributes = array_merge($attributes, $type->overrideAttributes());

        $field->fill($attributes);

        if ($field->isDirty()) {
            $field->save();
        }

        $this->cache->forget(static::CACHE_KEY_ALL_FIELDS);

        return $field;
    }

    /**
     * @param array $sorting
     */
    public function sorting(array $sorting)
    {
        foreach ($sorting as $i => $fieldId) {
            $this->field->where('id', $fieldId)->update(['sort' => $i]);
        }

        $this->cache->forget(static::CACHE_KEY_ALL_FIELDS);
    }

    /**
     * @param $id
     * @return bool|Field
     */
    public function delete($id)
    {
        $field = $this->field->findOrFail($id);

        if ($field) {
            $field->delete();

            $this->cache->forget(static::CACHE_KEY_ALL_FIELDS);

            return $field;
        }

        return false;
    }

    /**
     * Checks whether user has uncompleted fields.
     *
     * @param int $userId
     * @return bool
     * @todo we can't flush the cache because it uses a dynamic id
     */
    public function completed($userId)
    {
//      return $this->cache->rememberForever(sprintf(
//          static::CACHE_KEY_UNCOMPLETED,
//          $userId
//      ), function () use ($userId) {
        return $this->field
                ->where('required', true)
                ->whereDoesntHave('answers', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->count() == 0;

//      });
    }

    /**
     * @param Field $field
     * @param string $content
     * @param User $actor
     */
    public function addOrUpdateAnswer(Field $field, $content, User $actor)
    {
        /** @var Answer $answer */
        $answer = $field->answers()->firstOrNew([
            'user_id' => $actor->id,
        ]);

        $answer->content = $content;
        $answer->user()->associate($actor);

        $field->answers()->save($answer);

        $this->cache->forget(sprintf(
            static::CACHE_KEY_UNCOMPLETED,
            $actor->id
        ));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query()
    {
        return $this->field->newQuery()->orderBy('sort', 'desc');
    }

    protected function highestSort(): int
    {
        /** @var $max Field */
        $max = Field::orderBy('sort', 'desc')->first();

        return $max ? $max->sort + 1 : 0;
    }
}
