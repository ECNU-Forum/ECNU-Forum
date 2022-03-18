import Button from 'flarum/common/components/Button';
import BaseField from './BaseField';

export default class UrlField extends BaseField {
  editorInputAttrs() {
    let attrs = super.editorInputAttrs();

    attrs.type = 'url';
    attrs.placeholder = 'https://example.com';

    return attrs;
  }

  answerContent() {
    const value = this.value;

    if (!value) {
      return null;
    }

    return Button.component(
      {
        onclick: () => this.to(),
        className: 'Button Button--text',
        icon: 'fas fa-link',
      },
      value.replace(/^https?:\/\//, '')
    );
  }

  to() {
    const popup = window.open();
    popup.location = this.value;
  }
}
