import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import Button from 'flarum/common/components/Button';
import ItemList from 'flarum/common/utils/ItemList';
import Stream from 'flarum/common/utils/Stream';
import SearchState from 'flarum/forum/states/SearchState';
import RecipientSearch from '../search/RecipientSearch';
import User from 'flarum/common/models/User';
import Group from 'flarum/common/models/Group';

export default class AddRecipientModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

    this.selected = Stream(new ItemList());

    if (this.attrs.discussion) {
      // Adds recipients of the currently viewed discussion.
      this.assignInitialRecipients(this.attrs.discussion);
    } else if (this.attrs.selectedRecipients && this.attrs.selectedRecipients.toArray().length > 0) {
      // Adds previously selected recipients.
      this.selected().merge(this.attrs.selectedRecipients);
    } else {
      // Adds the current user in case there are no selected recipients yet and this is a new discussion.
      this.selected().add('users:' + app.session.user.id(), app.session.user);
    }

    this.recipientSearch = new SearchState();
  }

  isDismissible() {
    return false;
  }

  assignInitialRecipients(discussion) {
    discussion.recipientUsers().map((user) => {
      this.selected().add('users:' + user.id(), user);
    });
    discussion.recipientGroups().map((group) => {
      this.selected().add('groups:' + group.id(), group);
    });
  }

  className() {
    return 'AddRecipientModal';
  }

  title() {
    return this.attrs.discussion
      ? app.translator.trans('fof-byobu.forum.modal.titles.update_recipients', { title: <em>{this.attrs.discussion.title()}</em> })
      : app.translator.trans('fof-byobu.forum.modal.titles.add_recipients');
  }

  helpText() {
    return this.attrs.discussion
      ? app.translator.trans('fof-byobu.forum.modal.help.update_recipients')
      : app.translator.trans('fof-byobu.forum.modal.help.add_recipients');
  }

  content() {
    return [
      <div className="Modal-body">
        <div class="AddRecipientModal-help">{this.helpText()}</div>
        <div className="AddRecipientModal-form">
          {RecipientSearch.component({
            state: this.recipientSearch,
            selected: this.selected,
            discussion: this.attrs.discussion,
          })}
          <div className="AddRecipientModal-form-submit App-primaryControl">
            {Button.component(
              {
                type: 'submit',
                className: 'Button Button--primary',
                icon: 'fas fa-check',
              },
              app.translator.trans('fof-byobu.forum.buttons.submit')
            )}
            {Button.component(
              {
                onclick: this.hide.bind(this),
                className: 'Button Button--cancel',
              },
              app.translator.trans('fof-byobu.forum.buttons.cancel')
            )}
          </div>
        </div>
      </div>,
    ];
  }

  select(e) {
    // Ctrl + Enter submits the selection, just Enter completes the current entry
    if (e.metaKey || e.ctrlKey || this.selected.indexOf(this.index) !== -1) {
      if (this.selected().length) {
        this.$('form').submit();
      }
    }
  }

  onsubmit(e) {
    e.preventDefault();

    const discussion = this.attrs.discussion;
    const recipients = this.selected();

    let recipientGroups = [];
    let recipientUsers = [];

    recipients.toArray().forEach((recipient) => {
      if (recipient instanceof User) {
        recipientUsers.push(recipient);
      }
      if (recipient instanceof Group) {
        recipientGroups.push(recipient);
      }
    });

    // Recipients are updated here for existing discussions here.
    if (discussion) {
      discussion.save({ relationships: { recipientUsers, recipientGroups } }).then(() => {
        if (app.current instanceof DiscussionPage) {
          app.current.stream.update();
        }
        m.redraw();
      });
    }

    // Use the onsubmit callback to trigger an update in the DiscussionComposer
    if (this.attrs.onsubmit) this.attrs.onsubmit(recipients);

    app.modal.close();

    if (!this.attrs.discussion) {
      app.composer.show();
    }

    e.redraw = false;
  }
}
