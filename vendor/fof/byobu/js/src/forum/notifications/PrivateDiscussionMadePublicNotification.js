import app from 'flarum/forum/app';
import Notification from 'flarum/components/Notification';

export default class PrivateDiscussionMadePublicNotification extends Notification {
  icon() {
    return app.forum.attribute('byobu.icon-badge');
  }

  href() {
    const notification = this.props.notification;
    const discussion = notification.subject();

    return app.route.discussion(discussion);
  }

  content() {
    const user = this.props.notification.fromUser();
    return app.translator.trans('fof-byobu.forum.notifications.pd_made_public_text', {
      user: user,
    });
  }
}
