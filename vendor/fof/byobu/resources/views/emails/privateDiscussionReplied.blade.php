{!! $translator->trans('fof-byobu.email.body.private_discussion_replied', [
    '{recipient_display_name}' => $user->display_name,
    '{actor_display_name}' => $blueprint->post->user->display_name,
    '{discussion_title}' => $blueprint->post->discussion->title,
    '{post_url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->post->discussion_id, 'near' => $blueprint->post->number]),
]) !!}
