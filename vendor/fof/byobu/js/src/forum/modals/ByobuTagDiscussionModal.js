import app from 'flarum/forum/app';
import TagDiscussionModal from 'flarum/tags/components/TagDiscussionModal';

export default class ByobuTagDiscussionModal extends TagDiscussionModal {
  static isDismissible = false;

  onsubmit(e) {
    e.preventDefault();

    const tags = this.selected;

    if (this.attrs.resolve) this.attrs.resolve(tags);

    this.hide();
  }
}
