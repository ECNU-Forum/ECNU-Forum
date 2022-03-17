{!! $translator->trans('fof-byobu.email.body.private_discussion_created', [
    '{recipient_display_name}' => $user->display_name,
    '{actor_display_name}' => $blueprint->discussion->user->display_name,
    '{discussion_title}' => $blueprint->discussion->title,
    '{discussion_url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->discussion->id]),
]) !!}
