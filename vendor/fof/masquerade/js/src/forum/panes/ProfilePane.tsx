import app from 'flarum/forum/app';

import Component from 'flarum/common/Component';
import TypeFactory from '../types/TypeFactory';
import type Answer from '../../lib/models/Answer';
import type Field from 'src/lib/models/Field';
import type User from 'flarum/common/models/User';

export default class ProfilePane extends Component {
  answers!: Answer[];
  user!: User;

  oninit(vnode) {
    super.oninit(vnode);
    this.loading = true;

    this.answers = [];
    this.user = this.attrs.user;

    this.load(this.user);
  }

  view() {
    return (
      <div class="Masquerade-Bio">
        <div class="Fields">
          {app.store
            .all('masquerade-field')
            .sort((a, b) => a.sort() - b.sort())
            .map((field) => {
              const answer = this.answers.find((a) => a.field().id() === field.id());

              return this.field(field, answer?.content() || '');
            })}
        </div>
      </div>
    );
  }

  field(field: Field, content) {
    const type = TypeFactory.typeForField({
      field,
      value: content,
    });

    return type.answerField();
  }

  load() {
    this.answers = this.user.masqueradeAnswers();

    if (this.answers === false) {
      this.answers = [];
      app.store.find('users', this.user.id(), { include: 'masqueradeAnswers' }).then(() => {
        this.answers = this.user.masqueradeAnswers();
        m.redraw();
      });
    }

    m.redraw();
  }
}
