import app from 'flarum/forum/app';
import UserPage from 'flarum/forum/components/UserPage';
import PrivateDiscussionListState from '../states/PrivateDiscussionListState';
import Button from 'flarum/common/components/Button';
import Dropdown from 'flarum/common/components/Dropdown';
import ItemList from 'flarum/common/utils/ItemList';
import listItems from 'flarum/common/helpers/listItems';
import PrivateDiscussionList from './discussions/PrivateDiscussionList';
import PrivateComposing from './PrivateComposing';

export default class PrivateDiscussionsUserPage extends UserPage {
  oninit(vnode) {
    super.oninit(vnode);

    this.changeSort('latest');
  }

  show(user) {
    // We can not create the list in init because the user will not be available if it has to be loaded asynchronously
    this.list = new PrivateDiscussionListState({
      q: `byobu:${user.slug()} is:private`,
      sort: this.sort,
    });

    this.list.refresh();

    // We call the parent method after creating the list, this way the this.list property
    // is set before content() is called for the first time
    super.show(user);
  }

  handleChangeSort(sort, e) {
    e.preventDefault();

    this.changeSort(sort);
  }

  changeSort(sort) {
    this.sort = sort;
    this.loadUser(m.route.param('username'));
  }

  content() {
    return (
      <div className="DiscussionsUserPage">
        <div className="DiscussionsUserPage-toolbar">
          <ul className="DiscussionsUserPage-toolbar-action">{listItems(this.actionItems().toArray())}</ul>
          <ul className="DiscussionsUserPage-toolbar-view">{listItems(this.viewItems().toArray())}</ul>
        </div>
        <PrivateDiscussionList state={this.list}></PrivateDiscussionList>
      </div>
    );
  }

  actionItems() {
    let composing = new PrivateComposing(this.user);

    const items = new ItemList();

    if (app.session.user && app.forum.attribute('canStartPrivateDiscussion')) {
      items.add('start_private', composing.component());
    }

    return items;
  }

  viewItems() {
    const items = new ItemList();
    const sortMap = this.list.sortMap();

    const sortOptions = {};
    for (const i in sortMap) {
      sortOptions[i] = app.translator.trans('core.forum.index_sort.' + i + '_button');
    }

    items.add(
      'sort',
      Dropdown.component(
        {
          buttonClassName: 'Button',
          label: sortOptions[this.sort] || Object.keys(sortMap).map((key) => sortOptions[key])[0],
        },
        Object.keys(sortOptions).map((value) => {
          const label = sortOptions[value];
          const active = (this.sort || Object.keys(sortMap)[0]) === value;

          return Button.component(
            {
              icon: active ? 'fas fa-check' : true,
              onclick: this.handleChangeSort.bind(this, value),
              active: active,
            },
            label
          );
        })
      )
    );

    return items;
  }
}
