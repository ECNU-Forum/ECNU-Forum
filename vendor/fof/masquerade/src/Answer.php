<?php

namespace FoF\Masquerade;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $field_id
 * @property Field $field
 * @property int $user_id
 * @property User $user
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Answer extends AbstractModel
{
    public $timestamps = true;

    protected $table = 'fof_masquerade_answers';

    protected $fillable = [
        'user_id',
    ];

    protected $visible = [
        'user_id',
        'content',
        'field', // Used for the bio feature TODO: should use a relationship
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
