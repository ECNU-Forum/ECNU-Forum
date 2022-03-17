{!! $translator->trans('fof-merge-discussions.email.merged.body', [
    '{recipient_display_name}' => $user->display_name,
    '{actor_display_name}' => $blueprint->actor->display_name,
    '{merged_discussion_title}' => $blueprint->mergedDiscussion->title,
    '{discussion_title}' => $blueprint->discussion->title,
    '{discussion_url}' => $url->to('forum')->route('discussion', ['id' => $blueprint->discussion->id]),
]) !!}
