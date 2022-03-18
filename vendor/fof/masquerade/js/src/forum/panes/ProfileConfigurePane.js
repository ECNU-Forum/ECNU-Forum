import app from 'flarum/forum/app';

import Button from 'flarum/common/components/Button';
import Link from 'flarum/common/components/Link';
import TypeFactory from '../types/TypeFactory';
import Component from 'flarum/common/Component';

export default class ProfileConfigurePane extends Component {
  oninit(vnode) {
    super.oninit(vnode);
    this.loading = true;

    this.enforceProfileCompletion = app.forum.attribute('masquerade.force-profile-completion') || false;
    this.profileCompleted = app.forum.attribute('masquerade.profile-completed') || false;
    this.profileNowCompleted = false; // Show "after required" text
    this.answers = [];
    this.answerValues = {};
    this.user = this.attrs.user;
    this.load();

    // Show disabled state if everything is saved
    // Unless the profile isn't complete, in which case show enabled button so it's obvious you will need to save
    this.dirty = !this.profileCompleted;
  }

  view() {
    return (
      <form class="ProfileConfigurePane" onsubmit={this.update.bind(this)}>
        {!!(this.enforceProfileCompletion && !this.profileCompleted) && (
          <div class="Alert Alert--Error">{app.translator.trans('fof-masquerade.forum.alerts.profile-completion-required')}</div>
        )}

        <div class="Fields">
          {app.store
            .all('masquerade-field')
            .sort((a, b) => a.sort() - b.sort())
            .map((field) => {
              return this.field(field);
            })}
        </div>

        <Button type="submit" className="Button Button--primary" loading={this.loading} disabled={!this.dirty}>
          {app.translator.trans('fof-masquerade.forum.buttons.save-profile')}
        </Button>

        {!!this.profileNowCompleted && (
          <span class="Masquerade-NowCompleted">
            {app.translator.trans('fof-masquerade.forum.alerts.profile-completed', {
              a: <Link href={app.route('index')} />,
            })}
          </span>
        )}
      </form>
    );
  }

  field(field) {
    const type = TypeFactory.typeForField({
      field,
      set: this.set.bind(this, field),
      value: this.answerValues[field.id()],
    });

    return type.editorField();
  }

  load() {
    this.answers = this.user.masqueradeAnswers();

    if (this.answers === false) {
      this.answers = [];
      app.store.find('users', this.user.id(), { include: 'masqueradeAnswers' }).then(() => {
        this.answers = this.user.masqueradeAnswers();
        this.answerValues = {};

        app.store.all('masquerade-field').forEach((field) => {
          const answer = this.answers.find((a) => a.field().id() === field.id());

          this.answerValues[field.id()] = answer ? answer.content() : '';
        });

        this.loading = false;
        m.redraw();
      });
    } else {
      this.loading = false;

      app.store.all('masquerade-field').forEach((field) => {
        const answer = this.answers.find((a) => a.field().id() === field.id());

        this.answerValues[field.id()] = answer ? answer.content() : '';
      });
    }

    m.redraw();
  }

  set(field, value) {
    this.answerValues[field.id()] = value;
    this.dirty = true;
  }

  update(e) {
    e.preventDefault();

    this.loading = true;

    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/masquerade/configure/' + this.user.id(),
        body: this.answerValues,
      })
      .then((response) => {
        this.dirty = false;

        if (!this.profileCompleted) {
          this.profileCompleted = true;
          this.profileNowCompleted = true;
        }

        this.parseResponse(response);
      })
      .catch(() => {
        this.loading = false;
        m.redraw();
      });
  }

  parseResponse(response) {
    console.log(response);
    this.answers = app.store.pushPayload(response);
    this.loading = false;
    m.redraw();
  }
}
