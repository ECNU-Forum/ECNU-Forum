<?php

namespace FoF\Masquerade;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $prefix
 * @property string $icon
 * @property string $type
 * @property bool $required
 * @property string $validation
 * @property integer $sort
 * @property bool $on_bio
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|Answer[] $answers
 *
 * @property int $for A property used to pass the actor ID between the controller and serializer. Not actually in the DB
 */
class Field extends AbstractModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'fof_masquerade_fields';

    protected $casts = [
        'required' => 'boolean',
        'on_bio' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'prefix',
        'icon',
        'type',
        'required',
        'validation',
        'on_bio',
    ];

    protected $visible = [
        'name',
        'description',
        'prefix',
        'icon',
        'type',
        'required',
        'validation',
        'sort',
        'on_bio',
        'deleted_at', // Used to know if an API response was about deletion
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
