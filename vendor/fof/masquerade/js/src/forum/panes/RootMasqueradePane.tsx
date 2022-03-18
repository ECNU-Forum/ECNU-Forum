import UserPage from 'flarum/forum/components/UserPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import ProfilePane from './ProfilePane';
import ProfileConfigurePane from './ProfileConfigurePane';

export default class RootMasqueradePane extends UserPage {
  loading = true;

  oninit(vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  pageContentComponent() {
    if (!this.user) return null;

    if (this.user.canEditMasqueradeProfile()) return <ProfileConfigurePane user={this.user} />;
    else return <ProfilePane user={this.user} />;
  }

  show(user) {
    super.show(user);

    this.loading = false;
    m.redraw();
  }

  content() {
    return (
      <div class="MasqueradeRoot">
        {this.loading && <LoadingIndicator />}
        {this.pageContentComponent()}
      </div>
    );
  }
}
