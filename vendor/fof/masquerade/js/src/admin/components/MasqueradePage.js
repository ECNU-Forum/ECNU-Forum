import sortable from 'html5sortable/dist/html5sortable.es.js';

import app from 'flarum/admin/app';
import icon from 'flarum/common/helpers/icon';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Select from 'flarum/common/components/Select';
import Switch from 'flarum/common/components/Switch';
import Button from 'flarum/common/components/Button';
import saveSettings from 'flarum/admin/utils/saveSettings';
import withAttr from 'flarum/common/utils/withAttr';
import SelectFieldOptionEditor from './SelectFieldOptionEditor';

/* global m, $ */

export default class MasqueradePage extends ExtensionPage {
  oninit(vnode) {
    super.oninit(vnode);

    this.resetNew();
    this.loading = false;
    this.existing = [];
    this.loadExisting();
    this.enforceProfileCompletion = app.data.settings['masquerade.force-profile-completion'] === '1';
  }

  config() {
    sortable(this.element.querySelector('.js-sortable-fields'), {
      handle: 'legend',
    })[0].addEventListener('sortupdate', () => {
      const sorting = this.$('.js-sortable-fields > .Field')
        .map(function () {
          return $(this).data('id');
        })
        .get();

      this.updateSort(sorting);
    });
  }

  oncreate(vnode) {
    super.oncreate(vnode);

    this.config();
  }

  onupdate() {
    this.config();
  }

  content() {
    return m(
      '.ExtensionPage-settings.ProfileConfigurePane',
      m('.container', [
        m('h2', app.translator.trans('fof-masquerade.admin.general-options')),
        m(
          '.Form-group',
          Switch.component(
            {
              state: this.enforceProfileCompletion,
              onchange: (value) => {
                const saveValue = value ? '1' : '0';
                saveSettings({
                  'masquerade.force-profile-completion': saveValue,
                });
                this.enforceProfileCompletion = saveValue;
              },
            },
            app.translator.trans('fof-masquerade.admin.fields.force-user-to-completion')
          )
        ),
        m('h2', app.translator.trans('fof-masquerade.admin.fields.title')),
        m(
          'form.js-sortable-fields',
          this.existing.map((field) => {
            // Build array of fields to show.
            return this.addField(field);
          })
        ),
        this.addField(this.new),
      ])
    );
  }

  /**
   * Creates a field in the DOM.
   *
   * @param field
   * @returns {*}
   */
  addField(field) {
    let exists = field.id();

    return m(
      'fieldset.Field',
      {
        'data-id': field.id(),
        key: field.id(),
      },
      [
        m('legend', [
          exists
            ? [
                Button.component({
                  className: 'Button Button--icon Button--danger',
                  icon: 'fas fa-trash',
                  onclick: this.deleteField.bind(this, field),
                }),
                ' ',
              ]
            : null,
          m(
            'span.Field-toggle',
            {
              onclick: (e) => this.toggleField(e),
            },
            [
              app.translator.trans('fof-masquerade.admin.fields.' + (exists ? 'edit' : 'add'), {
                field: field.name(),
              }),
              ' ',
              icon('fas fa-caret-down'),
            ]
          ),
        ]),
        m('.Field-body', [
          m('.Form-group', [
            m('label', app.translator.trans('fof-masquerade.admin.fields.name')),
            m('input.FormControl', {
              value: field.name(),
              oninput: withAttr('value', this.updateExistingFieldInput.bind(this, 'name', field)),
            }),
            m('span.helpText', app.translator.trans('fof-masquerade.admin.fields.name-help')),
          ]),
          m('.Form-group', [
            m('label', app.translator.trans('fof-masquerade.admin.fields.description')),
            m('input.FormControl', {
              value: field.description(),
              oninput: withAttr('value', this.updateExistingFieldInput.bind(this, 'description', field)),
            }),
            m('span.helpText', app.translator.trans('fof-masquerade.admin.fields.description-help')),
          ]),
          m('.Form-group', [
            m('label', app.translator.trans('fof-masquerade.admin.fields.icon')),
            m('input.FormControl', {
              value: field.icon(),
              oninput: withAttr('value', this.updateExistingFieldInput.bind(this, 'icon', field)),
            }),
            m(
              'span.helpText',
              app.translator.trans('fof-masquerade.admin.fields.icon-help', {
                a: <a href="https://fontawesome.com/icons?m=free" target="_blank" />,
              })
            ),
          ]),
          m(
            '.Form-group',
            Switch.component(
              {
                state: field.on_bio(),
                onchange: this.updateExistingFieldInput.bind(this, 'on_bio', field),
              },
              app.translator.trans('fof-masquerade.admin.fields.on_bio')
            )
          ),
          m(
            '.Form-group',
            Switch.component(
              {
                state: field.required(),
                onchange: this.updateExistingFieldInput.bind(this, 'required', field),
              },
              app.translator.trans('fof-masquerade.admin.fields.required')
            )
          ),
          m('.Form-group', [
            m('label', app.translator.trans('fof-masquerade.admin.fields.type')),
            Select.component({
              onchange: (value) => {
                if (value === 'null') {
                  value = null;
                }

                this.updateExistingFieldInput('type', field, value);
              },
              options: this.availableTypes(),
              value: field.type(),
            }),
          ]),
          field.type() === 'select'
            ? SelectFieldOptionEditor.component({
                onchange: (value) => {
                  this.updateExistingFieldInput('validation', field, value);
                },
                value: field.validation(),
              })
            : null,
          field.type() === null
            ? m('.Form-group', [
                m('label', app.translator.trans('fof-masquerade.admin.fields.validation')),
                m('input.FormControl', {
                  value: field.validation(),
                  oninput: withAttr('value', this.updateExistingFieldInput.bind(this, 'validation', field)),
                }),
                m(
                  'span.helpText',
                  app.translator.trans('fof-masquerade.admin.fields.validation-help', {
                    a: <a href="https://laravel.com/docs/5.2/validation#available-validation-rules" target="_blank" />,
                  })
                ),
              ])
            : null,
          m(
            '.Form-group',
            m('.ButtonGroup', [
              Button.component(
                {
                  className: 'Button Button--primary',
                  loading: this.loading,
                  disabled: !this.readyToAdd(field),
                  onclick: exists ? this.updateExistingField.bind(this, field) : this.submitAddField.bind(this),
                },
                app.translator.trans('fof-masquerade.admin.buttons.' + (exists ? 'edit' : 'add') + '-field')
              ),
              exists
                ? Button.component(
                    {
                      className: 'Button Button--danger',
                      loading: this.loading,
                      onclick: this.deleteField.bind(this, field),
                    },
                    app.translator.trans('fof-masquerade.admin.buttons.delete-field')
                  )
                : null,
            ])
          ),
        ]),
      ]
    );
  }

  updateExistingFieldInput(what, field, value) {
    field.pushAttributes({
      [what]: value,
    });
  }

  /**
   * Sorts the fields.
   *
   * @param {Array} sorting
   */
  updateSort(sorting) {
    app
      .request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/masquerade/fields/order',
        body: {
          sort: sorting,
        },
      })
      .then(this.requestSuccess.bind(this));
  }

  /**
   * Opens and closes field configuration sets.
   *
   * @param e
   */
  toggleField(e) {
    $(e.target).parents('.Field').toggleClass('active');
  }

  /**
   * Deletes a field configuration set.
   *
   * @param field
   */
  deleteField(field) {
    field.delete().then(this.requestSuccess.bind(this));
  }

  /**
   * Saves the settings to the database and redraw the page
   *
   * @param e
   */
  submitAddField(e) {
    e.preventDefault();

    this.new
      .save(this.new.data.attributes)
      .then(this.requestSuccess.bind(this))
      .then(() => {
        this.resetNew();
      });

    m.redraw();
  }

  /**
   * Updates the value of one field.
   *
   * @param field
   */
  updateExistingField(field) {
    if (!field.id()) return;

    field.save(field.data.attributes).then(this.requestSuccess.bind(this));
  }

  /**
   * Parses result to update DOM.
   */
  requestSuccess() {
    this.existing = app.store.all('masquerade-field');

    // Update order in case the store order doesn't reflect the true ordering
    this.existing.sort((a, b) => {
      if (a.sort() < b.sort()) return -1;
      if (a.sort() > b.sort()) return 1;
      return 0;
    });

    this.loading = false;
    m.redraw();
  }

  /**
   * Retrieves list of fields.
   */
  loadExisting() {
    this.loading = true;

    return app
      .request({
        method: 'GET',
        url: app.forum.attribute('apiUrl') + '/masquerade/fields',
      })
      .then((result) => {
        app.store.pushPayload(result);

        this.requestSuccess();
      });
  }

  /**
   * Resets the new field.
   */
  resetNew() {
    this.new = app.store.createRecord('masquerade-field', {
      attributes: {
        name: '',
        description: '',
        prefix: '',
        icon: '',
        required: false,
        on_bio: false,
        type: null,
        validation: '',
      },
    });
  }

  /**
   * Checks whether creation field is completed.
   *
   * @returns boolean
   */
  readyToAdd(field) {
    if (field.name()) {
      return true;
    }

    return false;
  }

  /**
   * List of field types availables
   * @returns {Array}
   */
  availableTypes() {
    return {
      url: app.translator.trans('fof-masquerade.admin.types.url'),
      email: app.translator.trans('fof-masquerade.admin.types.email'),
      boolean: app.translator.trans('fof-masquerade.admin.types.boolean'),
      select: app.translator.trans('fof-masquerade.admin.types.select'),
      null: app.translator.trans('fof-masquerade.admin.types.advanced'),
    };
  }
}
