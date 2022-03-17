import type * as Mithril from 'mithril';
import username from 'flarum/common/helpers/username';
import User from 'flarum/common/models/User';
import Group from 'flarum/common/models/Group';
import LinkButton from 'flarum/common/components/LinkButton';
import classList from 'flarum/common/utils/classList';
import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';

export interface IRecipientLabelAttrs extends ComponentAttrs, Mithril.Attributes {
  recipient: User | Group;
  link: string;
}

export default class RecipientLabel extends Component<IRecipientLabelAttrs> {
  view(vnode: Mithril.Vnode) {
    const { recipient, link, ...newAttrs } = this.attrs;

    newAttrs.style = newAttrs.style || {};
    newAttrs.className = classList('RecipientLabel', newAttrs?.className);

    if (link && recipient instanceof User) {
      newAttrs.href = app.route.user(recipient);
    }

    let label: string;

    if (recipient instanceof User) {
      label = username(recipient);

      if (!newAttrs.href && recipient.id() !== app?.session?.user?.id()) {
        newAttrs.href = app.route.user(recipient);
      }
    } else if (recipient instanceof Group) {
      return <span class={newAttrs.className}>{recipient.namePlural()}</span>;
    } else {
      newAttrs.className += ' none';
      label = app.translator.trans('core.lib.username.deleted_text');
    }

    return <LinkButton {...newAttrs}>{label}</LinkButton>;
  }
}
