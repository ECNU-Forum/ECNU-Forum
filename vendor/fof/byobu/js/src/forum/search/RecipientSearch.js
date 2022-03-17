import app from 'flarum/forum/app';
import Search from 'flarum/forum/components/Search';
import UserSearchSource from './sources/UserSearchSource';
import GroupSearchSource from './sources/GroupSearchSource';
import ItemList from 'flarum/common/utils/ItemList';
import classList from 'flarum/common/utils/classList';
import extractText from 'flarum/common/utils/extractText';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import RecipientLabel from '../pages/labels/RecipientLabel';
import User from 'flarum/common/models/User';
import Group from 'flarum/common/models/Group';
import Tooltip from 'flarum/common/components/Tooltip';

export default class RecipientSearch extends Search {
  /**
   * Used to prevent duplicate IDs. Doesn't remove the possibility, but is extremely low.
   */
  inputUuid;

  oninit(vnode) {
    super.oninit(vnode);

    this.inputUuid = Math.random().toString(36).substring(2);
  }

  oncreate(vnode) {
    super.oncreate(vnode);

    const $search = this;

    this.$('.Search-results').on('click', (e) => {
      const target = this.$('.SearchResult.active');

      $search.addRecipient(target.data('index'));
      $search.$('.RecipientsInput').focus();
    });

    this.$('.Search-results').on('touchstart', (e) => {
      const target = this.$(e.target.parentNode);

      $search.addRecipient(target.data('index'));
      $search.$('.RecipientsInput').focus();
    });

    $('.RecipientsInput')
      .on('input', () => {
        clearTimeout(this.typingTimer);
        this.doSearch = false;
        this.typingTimer = setTimeout(() => {
          this.doSearch = true;
          m.redraw();
        }, 900);
      })
      .on('keydown', () => {
        clearTimeout(this.typingTimer);
      });

    super.oncreate(vnode);
  }

  view() {
    if (typeof this.state.getValue() === 'undefined') {
      this.state.setValue('');
    }

    const loading = this.state.getValue() && this.state.getValue().length >= 3;

    if (!this.sources) {
      this.sources = this.sourceItems().toArray();
    }

    return (
      <div role="search" className="Search">
        <div className="RecipientsInput-selected RecipientsLabel" aria-live="polite">
          <h4>{app.translator.trans('fof-byobu.forum.modal.labels.selected_users')}</h4>
          <p>{app.translator.trans('fof-byobu.forum.modal.help.selected_users')}</p>

          {this.attrs
            .selected()
            .toArray()
            .map((recipient) => {
              return (
                <Tooltip text={app.translator.trans('fof-byobu.forum.modal.help.click_user_to_remove_tooltip')}>
                  <RecipientLabel data-container="body" recipient={recipient} onclick={(e) => this.removeRecipient(recipient, e)} />
                </Tooltip>
              );
            })}
        </div>

        <div className="Form-group">
          <label for={`byobu-addrecipient-search-input-${this.inputUuid}`}>{app.translator.trans('fof-byobu.forum.modal.labels.search_field')}</label>

          <div className="AddRecipientModal-form-input Search-input">
            <input
              id={`byobu-addrecipient-search-input-${this.inputUuid}`}
              className={classList('RecipientsInput', 'FormControl', {
                open: !!this.state.getValue(),
                focused: !!this.state.getValue(),
                active: !!this.state.getValue(),
                loading: !!this.loadingSources,
              })}
              oncreate={(vnode) => vnode.dom.focus()}
              type="search"
              placeholder={extractText(app.translator.trans('fof-byobu.forum.input.search_recipients'))}
              value={this.state.getValue()}
              oninput={(e) => this.state.setValue(e.target.value)}
              onfocus={() => (this.hasFocus = true)}
              onblur={() => (this.hasFocus = false)}
            />
            <ul
              className={classList('Dropdown-menu', 'Search-results', 'fade', {
                in: !!loading,
              })}
            >
              {!this.doSearch
                ? LoadingIndicator.component({ size: 'tiny', className: 'Button Button--icon Button--link' })
                : this.sources.map((source) => source.view(this.state.getValue()))}
            </ul>
          </div>
        </div>
      </div>
    );
  }

  /**
   * Build an item list of SearchSources.
   *
   * @return {ItemList}
   */
  sourceItems() {
    const items = new ItemList();

    // Add user source based on permissions.
    if (
      (!this.attrs.discussion && app.forum.attribute('canStartPrivateDiscussionWithUsers')) ||
      (this.attrs.discussion && this.attrs.discussion.canEditUserRecipients())
    ) {
      items.add('users', new UserSearchSource());
    }

    // Add group source based on permissions.
    if (
      (!this.attrs.discussion && app.forum.attribute('canStartPrivateDiscussionWithGroups')) ||
      (this.attrs.discussion && this.attrs.discussion.canEditGroupRecipients())
    ) {
      items.add('groups', new GroupSearchSource());
    }

    return items;
  }

  /**
   * Adds a recipient.
   *
   * @param value
   */
  addRecipient(value) {
    let values = value.split(':'),
      type = values[0],
      id = values[1];

    let recipient = this.findRecipient(type, id);

    this.attrs.selected().add(value, recipient);

    this.state.clear();
  }

  /**
   * Removes a recipient.
   *
   * @param recipient
   */
  removeRecipient(recipient, e) {
    e.preventDefault();

    let type;

    if (recipient instanceof User) {
      type = 'users';
    }
    if (recipient instanceof Group) {
      type = 'groups';
    }

    this.attrs.selected().remove(type + ':' + recipient.id());
  }

  /**
   * Loads a recipient from the global store.
   *
   * @param store
   * @param id
   * @returns {Model}
   */
  findRecipient(store, id) {
    return app.store.getById(store, id);
  }
}
