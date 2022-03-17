import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Model from 'flarum/common/Model';
import Badge from 'flarum/common/components/Badge';
import Discussion from 'flarum/common/models/Discussion';
import User from 'flarum/common/models/User';
import Group from 'flarum/common/models/Group';
import Button from 'flarum/common/components/Button';
import DiscussionListItem from 'flarum/forum/components/DiscussionListItem';
import DiscussionHero from 'flarum/forum/components/DiscussionHero';
import DiscussionListState from 'flarum/forum/states/DiscussionListState';
import recipientsLabel from '../pages/labels/recipientsLabels';
import DiscussionControls from 'flarum/forum/utils/DiscussionControls';
import ItemList from 'flarum/common/utils/ItemList';
import AddRecipientModal from './../modals/AddRecipientModal';
import ByobuTagDiscussionModal from '../modals/ByobuTagDiscussionModal';
import DiscussionPage from 'flarum/components/DiscussionPage';

export default () => {
  attributes();
  badges();
  index();
  hero();
  apiInclude();
  controls();
};

const add = function (discussion, items, long) {
  let recipients = [];

  if (discussion.recipientUsers().length) {
    recipients = recipients.concat(discussion.recipientUsers());
  }

  if (discussion.recipientGroups().length) {
    recipients = recipients.concat(discussion.recipientGroups());
  }

  if (recipients && recipients.length) {
    if (long) {
      items.add('recipients', recipientsLabel(recipients), 10);
    } else {
      items.add('recipients', recipientsLabel(recipients, { link: true }), 4);
    }
  }
};

function badges() {
  extend(Discussion.prototype, 'badges', function (badges) {
    if (this.recipientUsers().length || this.recipientGroups().length) {
      badges.add(
        'private',
        Badge.component({
          type: 'private',
          label: app.translator.trans('fof-byobu.forum.badges.is_private.tooltip'),
          icon: app.forum.data.attributes['byobu.icon-badge'],
        }),
        10
      );
    }
  });
}

function index() {
  extend(DiscussionListItem.prototype, 'infoItems', function (items) {
    const discussion = this.attrs.discussion;

    add(discussion, items, true);
  });
}

function hero() {
  extend(DiscussionHero.prototype, 'items', function (items) {
    const discussion = this.attrs.discussion;

    add(discussion, items, false);
  });
}

function apiInclude() {
  extend(DiscussionListState.prototype, 'requestParams', function (params) {
    params.include.push('recipientUsers');
    params.include.push('recipientGroups');
  });
}

function controls() {
  extend(DiscussionControls, 'moderationControls', function (items, discussion) {
    if (discussion.canEditRecipients()) {
      items.add(
        'recipients',
        Button.component(
          {
            icon: app.forum.data.attributes['byobu.icon-badge'],
            onclick: () => app.modal.show(AddRecipientModal, { discussion }),
          },
          app.translator.trans('fof-byobu.forum.buttons.edit_recipients')
        )
      );
    }
    if (discussion && discussion.recipientUsers().find((user) => user.id() === app.session.user.id())) {
      items.add(
        'remove',
        Button.component(
          {
            icon: 'fas fa-user-slash',
            onclick: () => {
              if (discussion) {
                let recipients = new ItemList();
                discussion.recipientUsers().map((user) => {
                  if (app.session.user.id() !== user.id()) {
                    recipients.add('users:' + user.id(), user);
                  }
                });

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

                discussion
                  .save({
                    relationships: {
                      recipientUsers,
                      recipientGroups,
                    },
                  })
                  .then(() => app.history.back());
              }
            },
          },
          app.translator.trans('fof-byobu.forum.buttons.remove_from_discussion')
        )
      );

      if (discussion?.isPrivateDiscussion?.() && discussion?.canMakePublic?.()) {
        items.add(
          'transform-public',
          <Button
            icon="far fa-eye"
            onclick={() => {
              if (discussion && confirm(app.translator.trans('fof-byobu.forum.confirm.make_public'))) {
                const recipientGroups = [];
                const recipientUsers = [];

                if (flarum.extensions['flarum-tags']) {
                  new Promise((resolve, reject) => {
                    app.modal.show(ByobuTagDiscussionModal, { discussion, resolve, reject });
                  }).then((tags) => {
                    discussion.save({ relationships: { recipientUsers, recipientGroups }, public: discussion.id() }).then(() => {
                      discussion.save({ relationships: { tags } }).then(() => {
                        if (app.current.matches(DiscussionPage)) {
                          app.current.get('stream').update();
                        }
                        m.redraw();
                      });
                    });
                  });
                } else {
                  discussion.save({ relationships: { recipientUsers, recipientGroups }, public: discussion.id() }).then(() => m.redraw());
                }
              }
            }}
          >
            {app.translator.trans('fof-byobu.forum.buttons.make_public')}
          </Button>
        );
      }
    }
  });
}

function attributes() {
  Discussion.prototype.recipientUsers = Model.hasMany('recipientUsers');
  Discussion.prototype.oldRecipientUsers = Model.hasMany('oldRecipientUsers');
  Discussion.prototype.recipientGroups = Model.hasMany('recipientGroups');
  Discussion.prototype.oldRecipientGroups = Model.hasMany('oldRecipientGroups');

  Discussion.prototype.canEditRecipients = Model.attribute('canEditRecipients');
  Discussion.prototype.canEditUserRecipients = Model.attribute('canEditUserRecipients');
  Discussion.prototype.canEditGroupRecipients = Model.attribute('canEditGroupRecipients');
  Discussion.prototype.canEditGroupRecipients = Model.attribute('canEditGroupRecipients');
  Discussion.prototype.canMakePublic = Model.attribute('canMakePublic');

  Discussion.prototype.isPrivateDiscussion = Model.attribute('isPrivateDiscussion');
}
