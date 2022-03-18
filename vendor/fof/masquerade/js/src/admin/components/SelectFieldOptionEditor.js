import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';
import icon from 'flarum/common/helpers/icon';

/* global m */

export default class SelectFieldOptionEditor extends Component {
  oninit(vnode) {
    super.oninit(vnode);

    this.newOption = '';
  }

  view() {
    return m('.Form-group', [
      m('label', app.translator.trans('fof-masquerade.admin.fields.options')),
      m(
        'table',
        m(
          'tbody',
          this.options().map((option, optionIndex) =>
            m('tr', [
              m(
                'td',
                m('input[type=text].FormControl', {
                  oninput: (event) => {
                    this.updateOption(optionIndex, event.target.value);
                  },
                  value: option,
                })
              ),
              m(
                'td',
                m(
                  'button.Button',
                  {
                    onclick: () => {
                      this.moveOption(optionIndex, -1);
                    },
                  },
                  icon('fas fa-chevron-up')
                )
              ),
              m(
                'td',
                m(
                  'button.Button',
                  {
                    onclick: () => {
                      this.moveOption(optionIndex, 1);
                    },
                  },
                  icon('fas fa-chevron-down')
                )
              ),
              m(
                'td',
                m(
                  'button.Button.Button--danger',
                  {
                    onclick: () => {
                      this.deleteOption(optionIndex);
                    },
                  },
                  icon('fas fa-times')
                )
              ),
            ])
          )
        )
      ),
      m('.helpText', app.translator.trans('fof-masquerade.admin.fields.option-comma-warning')),
      m(
        'table',
        m('tbody'),
        m('tr', [
          m(
            'td',
            m('input[type=text].FormControl', {
              onchange: (event) => {
                this.newOption = event.target.value;
              },
              value: this.newOption,
              placeholder: app.translator.trans('fof-masquerade.admin.fields.option-new'),
            })
          ),
          m(
            'td',
            m(
              'button.Button.Button--primary',
              {
                onclick: () => {
                  this.addOption();
                },
              },
              icon('fas fa-plus')
            )
          ),
        ])
      ),
    ]);
  }

  updateRules(options) {
    // We ignore other existing rules, they would probably be leftovers from another field type when changing types
    this.attrs.onchange('in:' + options.join(','));
  }

  options() {
    const rules = this.attrs.value.split('|');

    let options = [];

    rules.forEach((rule) => {
      const parts = rule.split(':', 2);

      if (parts[0] === 'in') {
        options = parts[1].split(',');
      }
    });

    return options;
  }

  updateOption(index, value) {
    let options = this.options();

    options[index] = value;

    this.updateRules(options);
  }

  moveOption(index, moveIndex) {
    let options = this.options();

    const newIndex = index + moveIndex;

    if (newIndex < 0 || newIndex > options.length - 1) {
      return;
    }

    const move = options.splice(index, 1);

    options.splice(newIndex, 0, move[0]);

    this.updateRules(options);
  }

  deleteOption(index) {
    let options = this.options();

    options.splice(index, 1);

    this.updateRules(options);
  }

  addOption() {
    if (this.newOption === '') {
      return;
    }

    let options = this.options();

    options.push(this.newOption);

    this.newOption = '';

    this.updateRules(options);
  }
}
