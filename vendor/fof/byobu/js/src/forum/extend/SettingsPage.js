import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import SettingsPage from 'flarum/forum/components/SettingsPage';
import Switch from 'flarum/common/components/Switch';

export default () => {
  privacyToggle();
};

function privacyToggle() {
  extend(SettingsPage.prototype, 'privacyItems', function (items) {
    items.add(
      'byobu-block-dm',
      Switch.component(
        {
          state: this.user.blocksPd(),
          onchange: (value) => {
            this.blocksPdLoading = true;

            this.user.save({ blocksPd: value }).then(() => {
              this.blocksPdLoading = false;
              m.redraw();
            });
          },
          loading: this.blocksPdLoading,
        },
        app.translator.trans('fof-byobu.forum.user.settings.block_pd')
      )
    );
  });
}
