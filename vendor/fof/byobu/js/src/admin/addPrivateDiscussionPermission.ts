import app from 'flarum/admin/app';

export default function () {
  const byobuData = app.extensionData.for('fof-byobu');

  byobuData
    .registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.create_private_discussions_with_users'),
        permission: 'discussion.startPrivateDiscussionWithUsers',
        tagScoped: false,
      },
      'start',
      95
    )
    .registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.create_private_discussions_with_groups'),
        permission: 'discussion.startPrivateDiscussionWithGroups',
        tagScoped: false,
      },
      'start',
      95
    )
    .registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.create_private_discussions_with_blocking_users'),
        permission: 'startPrivateDiscussionWithBlockers',
        tagScoped: false,
      },
      'start',
      95
    )
    .registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.edit_user_recipients'),
        permission: 'discussion.editUserRecipients',
        tagScoped: false,
      },
      'moderate',
      95
    )
    .registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.edit_group_recipients'),
        permission: 'discussion.editGroupRecipients',
        tagScoped: false,
      },
      'moderate',
      95
    )
    .registerPermission(
      {
        icon: 'fas fa-flag',
        label: app.translator.trans('fof-byobu.admin.permission.view_private_discussions-when-flagged'),
        permission: 'user.viewPrivateDiscussionsWhenFlagged',
        tagScoped: false,
      },
      'moderate',
      95
    );

  if (app.data.settings['fof-byobu.makePublic']) {
    byobuData.registerPermission(
      {
        icon: 'far fa-map',
        label: app.translator.trans('fof-byobu.admin.permission.make_private_into_public'),
        permission: 'discussion.makePublic',
        tagScoped: false,
      },
      'reply',
      95
    );
  }
}
