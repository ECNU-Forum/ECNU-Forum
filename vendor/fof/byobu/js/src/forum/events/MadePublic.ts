import app from 'flarum/forum/app';
import EventPost from 'flarum/forum/components/EventPost';

export default class MadePublic extends EventPost {
  static initAttrs(attrs: any) {
    super.initAttrs(attrs);
  }

  icon() {
    return app.forum.attribute('byobu.icon-postAction');
  }

  descriptionKey() {
    return 'fof-byobu.forum.post.recipients_modified.made_public';
  }
}
