import Button from 'flarum/common/components/Button';
import BaseField from './BaseField';

export default class EmailField extends BaseField {
  editorInputAttrs() {
    let attrs = super.editorInputAttrs();

    attrs.type = 'email';
    attrs.placeholder = 'you@example.com';

    return attrs;
  }

  answerContent() {
    const value = this.value;

    if (!value) {
      return null;
    }

    const email = value
      .split(/@|\./)
      .map((segment) => {
        return segment.replace(/(.{2})./g, '$1*');
      })
      .join('*');

    return Button.component(
      {
        onclick: () => this.mailTo(),
        className: 'Button Button--text',
        icon: 'far fa-envelope',
      },
      email
    );
  }

  mailTo() {
    window.location = 'mailto:' + this.value;
  }
}
