import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import { ComponentAttrs } from 'flarum/common/Component';
import { Vnode, Children } from 'mithril';
import listItems from 'flarum/common/helpers/listItems';
import ItemList from 'flarum/common/utils/ItemList';
import MobileTabItem from './MobileTabItem';
import LinkButton from 'flarum/common/components/LinkButton';
import Button from 'flarum/common/components/Button';
import LogInModal from 'flarum/forum/components/LogInModal';

import MobileTabSessionDropdown from './MobileTabSessionDropdown';

export default class MobileTab extends Component {
  view(vnode: Vnode<ComponentAttrs, this>): Children {
    return (
      <nav className="MobileTab">
        <ul className="MobileTab-items">{listItems(this.items().toArray())}</ul>
      </nav>
    );
  }

  items(): ItemList<Children> {
    const items = new ItemList<Children>();

    items.add('home', <MobileTabItem route="/" icon="fas fa-home" label={app.translator.trans('acpl-mobile-tab.forum.home')} />, 100);

    if ('flarum-tags' in flarum.extensions) {
      items.add(
        'tags',
        <MobileTabItem route={app.route('tags')} icon="fas fa-tags" label={app.translator.trans('flarum-tags.forum.index.tags_link')} />,
        90
      );
    }

    if (app.session.user) {
      const unread = app.session.user.unreadNotificationCount();
      // The default Flarum component opens as a dropdown on mobile if the drawer is not open
      items.add(
        'notifications',
        <LinkButton
          href={app.route('notifications')}
          icon="fas fa-bell"
          title={app.translator.trans('core.forum.notifications.title')}
          className="Dropdown NotificationsDropdown"
        >
          {unread ? <span className="NotificationsDropdown-unread">{unread}</span> : ''}
          {app.translator.trans('core.forum.notifications.title')}
        </LinkButton>,
        80
      );

      items.add('session', <MobileTabSessionDropdown />, 70);
    } else {
      items.add(
        'logIn',
        <Button icon="fas fa-user" className="Button Button--link" onclick={() => app.modal.show(LogInModal, {})}>
          {app.translator.trans('core.forum.header.log_in_link')}
        </Button>,
        70
      );
    }

    return items;
  }
}
