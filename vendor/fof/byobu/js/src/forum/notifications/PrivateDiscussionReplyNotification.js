import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';

export default class PrivateDiscussionReplyNotification extends Notification {
  icon() {
    return 'fas fa-reply';
  }

  href() {
    const notification = this.attrs.notification;
    const discussion = notification.subject();
    const content = notification.content() || {};

    return app.route.discussion(discussion, content.postNumber);
  }

  content() {
    const user = this.attrs.notification.fromUser();
    return app.translator.trans('fof-byobu.forum.notifications.pd_reply_text', {
      user: user,
    });
  }
}
