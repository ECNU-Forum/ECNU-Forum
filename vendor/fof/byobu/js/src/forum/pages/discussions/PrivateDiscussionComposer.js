import app from 'flarum/forum/app';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import AddRecipientModal from '../../modals/AddRecipientModal';
import ItemList from 'flarum/common/utils/ItemList';
import recipientCountLabel from '../labels/recipientCountLabel';
import User from 'flarum/common/models/User';
import Group from 'flarum/common/models/Group';

export default class PrivateDiscussionComposer extends DiscussionComposer {
  /**
   * Tells other extensions that this composer is a Byobu composer.
   */
  _isByobuComposer = true;

  static initAttrs(attrs) {
    super.initAttrs(attrs);

    attrs.titlePlaceholder = app.translator.trans('fof-byobu.forum.composer_private_discussion.title_placeholder');
    attrs.submitLabel = app.translator.trans('fof-byobu.forum.composer_private_discussion.submit_button');
  }

  oninit(vnode) {
    super.oninit(vnode);

    this.composer.fields.recipients = this.attrs.recipients || new ItemList();

    this.composer.fields.recipientUsers = this.attrs.recipientUsers || [];
    this.composer.fields.recipientGroups = this.attrs.recipientGroups || [];

    const username = m.route.param('username');

    if (typeof username !== 'undefined') {
      this.addDefaultRecipients(username);
    }
  }

  data() {
    let data = super.data();

    const users = [];
    const groups = [];

    if (this.composer.fields.recipients !== undefined) {
      this.composer.fields.recipients.toArray().forEach((recipient) => {
        if (recipient instanceof User) {
          users.push(recipient);
        }

        if (recipient instanceof Group) {
          groups.push(recipient);
        }
      });
    }

    data.relationships = data.relationships || {};

    if (users.length) {
      data.relationships.recipientUsers = users;
    }

    if (groups.length) {
      data.relationships.recipientGroups = groups;
    }

    delete data.relationships.tags;

    return data;
  }

  chooseRecipients() {
    app.modal.show(AddRecipientModal, {
      selectedRecipients: this.composer.fields.recipients,
      onsubmit: (recipients) => {
        this.composer.fields.recipients = recipients;

        // Focus on recipient autocomplete field.
        this.$('.RecipientsInput').focus();
      },
    });
  }

  headerItems() {
    let items = super.headerItems();

    items.remove('tags');

    if (app.session.user && app.forum.attribute('canStartPrivateDiscussion')) {
      const recipients = this.composer.fields.recipients.toArray();

      items.add(
        'recipients',
        <a className="PrivateDiscussionComposer-changeRecipients" onclick={this.chooseRecipients.bind(this)}>
          {recipients.length ? (
            recipientCountLabel(recipients.length)
          ) : (
            <span className="RecipientLabel none">{app.translator.trans('fof-byobu.forum.buttons.add_recipients')}</span>
          )}
        </a>,
        5
      );
    }

    return items;
  }

  addDefaultRecipients(username) {
    const user = app.store.getBy('users', 'username', username);

    this.composer.fields.recipients.add('users:' + app.session.user.id(), app.session.user);

    if (user.id() !== app.session.user.id()) {
      this.composer.fields.recipients.add('users:' + user.id(), user);
    }
  }

  onsubmit() {
    this.loading = true;

    const recipients = this.composer.fields.recipients.toArray();

    if (recipients.length < 2) {
      this.chooseRecipients();

      this.loading = false;
    } else {
      const data = this.data();

      app.store
        .createRecord('discussions')
        .save(data)
        .then((discussion) => {
          if (app.cache.discussionList) {
            app.cache.discussionList.refresh();
          }
          m.route.set(app.route.discussion(discussion));

          app.composer.hide();
        }, this.loaded.bind(this));
    }
  }
}
