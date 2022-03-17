import app from 'flarum/forum/app';
import EventPost from 'flarum/forum/components/EventPost';
import recipientsLabel from '../pages/labels/recipientsLabels';

export default class RecipientsModified extends EventPost {
  static initAttrs(attrs) {
    super.initAttrs(attrs);

    function diff(diff1, diff2, store) {
      return diff1.filter((item) => diff2.indexOf(item) === -1).map((id) => app.store.getById(store, id));
    }

    const content = attrs.post.content();

    // For event posts existing before groups functionality.
    if (!content['new'] && content.length == 2) {
      const oldRecipients = attrs.post.content()[0];
      const newRecipients = attrs.post.content()[1];
      attrs.added = diff(newRecipients, oldRecipients, 'users');
      attrs.removed = diff(oldRecipients, newRecipients, 'users');
    } else {
      let usersAdded = diff(content['new']['users'], content['old']['users'], 'users');
      let usersRemoved = diff(content['old']['users'], content['new']['users'], 'users');
      let groupsAdded = diff(content['new']['groups'], content['old']['groups'], 'groups');
      let groupsRemoved = diff(content['old']['groups'], content['new']['groups'], 'groups');

      attrs.added = usersAdded.concat(groupsAdded);
      attrs.removed = usersRemoved.concat(groupsRemoved);
    }
  }

  icon() {
    return app.forum.attribute('byobu.icon-postAction');
  }

  descriptionKey() {
    var localeBase = 'fof-byobu.forum.post.recipients_modified.';

    if (this.attrs.added.length) {
      if (this.attrs.removed.length) {
        return localeBase + 'added_and_removed';
      }

      return localeBase + 'added';
    }

    return localeBase + 'removed';
  }

  descriptionData() {
    const data = {};

    if (this.attrs.added.length) {
      data.added = recipientsLabel(this.attrs.added, { link: true });
    }

    if (this.attrs.removed.length) {
      data.removed = recipientsLabel(this.attrs.removed, { link: true });
    }

    return data;
  }
}
