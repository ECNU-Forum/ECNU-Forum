import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Badge from 'flarum/common/components/Badge';
import icon from 'flarum/common/helpers/icon';

import type Mithril from 'mithril';

export default class ByobuSetingsPage extends ExtensionPage {
  oninit(vnode: Mithril.Vnode<any, any>) {
    super.oninit(vnode);

    this.badgeDefault = 'fas fa-map';
    this.postActionDefault = 'far fa-map';
  }

  content() {
    const helpText = flarum.extensions['flarum-tags']
      ? app.translator.trans('flarum-tags.admin.edit_tag.icon_text', {
          a: <a href="https://fontawesome.com/icons?m=free" tabindex="-1" />,
        })
      : '';

    return [
      <div className="ByobuSettingsPage">
        <div className="container">
          <div className="Form">
            <div className="Form-group">
              {this.buildSettingComponent({
                type: 'string',
                setting: 'fof-byobu.icon-badge',
                label: app.translator.trans('fof-byobu.admin.settings.badge-icon'),
                help: (
                  <div>
                    <Badge icon={this.setting('fof-byobu.icon-badge').toJSON() || this.badgeDefault}></Badge> {helpText}
                  </div>
                ),
                placeholder: this.badgeDefault,
              })}
              {this.buildSettingComponent({
                type: 'string',
                setting: 'fof-byobu.icon-postAction',
                label: app.translator.trans('fof-byobu.admin.settings.post-event-icon'),
                help: (
                  <div>
                    {icon(this.setting('fof-byobu.icon-postAction').toJSON() || this.postActionDefault)} {helpText}
                  </div>
                ),
                placeholder: this.postActionDefault,
              })}
              {this.buildSettingComponent({
                type: 'boolean',
                setting: 'fof-byobu.makePublic',
                label: app.translator.trans('fof-byobu.admin.settings.enable-make-public-option'),
                help: app.translator.trans('fof-byobu.admin.settings.enable-make-public-option-help'),
              })}
            </div>
            <div className="Form-group">{this.submitButton()}</div>
          </div>
        </div>
      </div>,
    ];
  }
}
