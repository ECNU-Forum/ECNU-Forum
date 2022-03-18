<?php

namespace FoF\Masquerade\Gambits;

use Flarum\Filter\FilterInterface;
use Flarum\Filter\FilterState;
use Flarum\Search\AbstractRegexGambit;
use Flarum\Search\SearchState;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

class AnswerGambit extends AbstractRegexGambit implements FilterInterface
{
    protected function getGambitPattern(): string
    {
        return 'answer:(.+)';
    }

    public function apply(SearchState $search, $bit): bool
    {
        if (!$search->getActor()->hasPermission('fof.masquerade.view-profile')) {
            return false;
        }

        return parent::apply($search, $bit);
    }

    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $bit = $matches[1];

        $this->constrain($search->getQuery(), $bit, $negate);
    }

    public function getFilterKey(): string
    {
        return 'answer';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate)
    {
        $this->constrain($filterState->getQuery(), $filterValue, $negate);
    }

    protected function constrain(Builder $query, string $bit, bool $negate)
    {
        $query->whereExists(function (Builder $query) use ($bit) {
            $query->select($query->raw(1))
                ->from('fof_masquerade_answers')
                ->where('users.id', new Expression('user_id'))
                ->where('content', 'like', "%$bit%");
        }, 'and', $negate);
    }
}
