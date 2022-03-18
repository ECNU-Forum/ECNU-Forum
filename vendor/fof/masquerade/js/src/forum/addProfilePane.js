import app from 'flarum/forum/app';

import { extend } from 'flarum/common/extend';
import LinkButton from 'flarum/common/components/LinkButton';
import UserPage from 'flarum/forum/components/UserPage';
import RootMasqueradePane from './panes/RootMasqueradePane';

export default function addProfilePane() {
  app.routes['fof-masquerade'] = {
    path: '/u/:username/masquerade',
    resolver: {
      onmatch() {
        if (!app.forum.attribute('canViewMasquerade')) throw new Error();

        return RootMasqueradePane;
      },
    },
  };

  extend(UserPage.prototype, 'navItems', function (items) {
    if (app.forum.attribute('canViewMasquerade') || this.user.canEditMasqueradeProfile()) {
      const edit = this.user.canEditMasqueradeProfile();

      items.add(
        'masquerade',
        LinkButton.component(
          {
            href: app.route('fof-masquerade', { username: this.user.slug() }),
            icon: 'far fa-id-card',
            'data-editProfile': edit,
          },
          app.translator.trans(`fof-masquerade.forum.buttons.${edit ? 'edit' : 'view'}-profile`)
        ),
        200
      );
    }
  });
}
