import app from 'flarum/forum/app';
import SessionDropdown from 'flarum/forum/components/SessionDropdown';
import avatar from 'flarum/common/helpers/avatar';

export default class MobileTabSessionDropdown extends SessionDropdown {
  getButtonContent(vnode) {
    const user = app.session.user;

    // The username can be long, so it is better to display "Profile"
    return [avatar(user), ' ', <span className="Button-label">{app.translator.trans('core.forum.header.profile_button')}</span>];
  }
}
