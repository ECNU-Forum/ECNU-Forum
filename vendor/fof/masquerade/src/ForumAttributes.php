<?php

namespace FoF\Masquerade;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Masquerade\Repositories\FieldRepository;

class ForumAttributes
{
    protected $settings;
    protected $fields;

    public function __construct(SettingsRepositoryInterface $settings, FieldRepository $fields)
    {
        $this->settings = $settings;
        $this->fields = $fields;
    }

    public function __invoke(ForumSerializer $serializer): array
    {
        $actor = $serializer->getActor();

        return [
            'masquerade.force-profile-completion' => (bool)$this->settings->get('masquerade.force-profile-completion', false),
            'masquerade.profile-completed' => $actor->isGuest() ? false : $this->fields->completed($actor->id),
            'canViewMasquerade' => $actor->can('fof.masquerade.view-profile'),
        ];
    }
}
