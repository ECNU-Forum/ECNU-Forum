import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

import SettingsPage from 'flarum/forum/components/SettingsPage';
import SessionDropdown from 'flarum/forum/components/SessionDropdown';
import HeaderSecondary from 'flarum/forum/components/HeaderSecondary';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Select from 'flarum/common/components/Select';
import FieldSet from 'flarum/common/components/FieldSet';
import Switch from 'flarum/common/components/Switch';
import icon from 'flarum/common/helpers/icon';

import { setTheme } from '../common/setSelectedTheme';
import fixInvalidThemeSetting from './fixInvalidThemeSetting';
import * as perDevice from './helpers/perDeviceSetting';
import getTheme from './getTheme';
import Themes from '../common/Themes';

// custom function for translations makes it a lot cleaner
const trans = (key) => app.translator.trans(`fof-nightmode.forum.user.settings.${key}`);

const getIsLight = (theme) => theme === Themes.LIGHT || (theme === Themes.AUTO && !window.matchMedia('(prefers-color-scheme: dark)').matches);

const toggleThrough = (current) => {
  if (current === Themes.AUTO) {
    return Themes.LIGHT;
  }

  if (current === Themes.LIGHT) {
    return Themes.DARK;
  }

  return Themes.AUTO;
};

export default function () {
  extend(SettingsPage.prototype, 'settingsItems', function (items) {
    const { user } = app.session;

    const PerDevice = !!user.preferences().fofNightMode_perDevice;

    if (PerDevice) {
      fixInvalidThemeSetting();
    }

    const doesNotSupportAuto = !window.matchMedia('not all and (prefers-color-scheme), (prefers-color-scheme)').matches;

    let currentTheme = getTheme();

    const options = { 0: trans('options.auto'), 1: trans('options.day'), 2: trans('options.night') };

    if (doesNotSupportAuto) {
      delete options['0'];

      if (currentTheme === Themes.AUTO) {
        currentTheme = app.forum.attribute('fofNightMode_autoUnsupportedFallback');
      }
    }

    items.add(
      'fof-nightmode',
      FieldSet.component(
        {
          label: trans('heading'),
          className: 'Settings-theme',
        },
        [
          <p className="description">{trans('description')}</p>,
          <p className="description">{trans('description2')}</p>,
          doesNotSupportAuto ? <p class="description NightMode-autoUnsupported">{trans('auto_unsupported')}</p> : null,
          Switch.component(
            {
              className: 'Settings-theme--per_device_cb',
              state: PerDevice,
              onchange: (checked) => {
                if (checked) {
                  // save current theme as this device's default
                  perDevice.set(currentTheme);
                } else {
                  perDevice.remove();
                }

                user
                  .savePreferences({
                    fofNightMode_perDevice: checked,
                  })
                  .then(() => {
                    // need to force-update selected theme (as it's only set
                    // on a page load and redraw doesn't count as a page load)
                    setTheme();
                  });
              },
            },
            trans('device_specific_setting_checkbox')
          ),
          Select.component({
            value: currentTheme,
            className: 'Settings-theme--input',
            onchange: (e) => {
              if (PerDevice) {
                perDevice.set(e);

                setTheme();
                return;
              }

              user
                .savePreferences({
                  fofNightMode: e,
                })
                .then(() => {
                  m.redraw();

                  // need to force-update selected theme (as it's only set
                  // on a page load and redraw doesn't count as a apge load)
                  setTheme();
                });
            },
            options,
          }),
          <p className="Settings-theme--selection_description">
            {currentTheme === Themes.AUTO
              ? trans('option_descriptions.auto')
              : currentTheme === Themes.LIGHT
              ? trans('option_descriptions.day')
              : currentTheme === Themes.DARK
              ? trans('option_descriptions.night')
              : // prevents nasty paragraph switching
                LoadingIndicator.component()}
          </p>,
        ]
      )
    );
  });

  extend(HeaderSecondary.prototype, 'items', function (items) {
    if (app.session.user) return;

    const theme = getTheme();
    const isLight = getIsLight(theme);

    items.add(
      'nightmode',
      <Button
        className="Button Button--flat"
        onclick={() => {
          const newTheme = toggleThrough(theme);

          perDevice.set(newTheme);
          setTheme();
        }}
        icon={theme === Themes.AUTO ? 'fas fa-adjust' : `far fa-${isLight ? 'sun' : 'moon'}`}
      >
        {app.translator.trans('fof-nightmode.forum.header.nightmode_button')}
      </Button>,
      15
    );
  });

  extend(SessionDropdown.prototype, 'items', function (items) {
    if (!app.session.user) return;

    const user = app.session.user;
    const isLight = getIsLight(getTheme());

    // Add night mode link to session dropdown
    items.add(
      isLight ? 'nightmode' : 'daymode',
      Button.component(
        {
          icon: `far fa-${isLight ? 'moon' : 'sun'}`,
          onclick: () => {
            const val = isLight ? Themes.DARK : Themes.LIGHT;

            if (!!user.preferences().fofNightMode_perDevice) {
              perDevice.set(val);
              setTheme();
              return;
            }

            user
              .savePreferences({
                fofNightMode: val,
              })
              .then(() => {
                // need to force-update selected theme (as it's only set
                // on a page load and redraw doesn't count as a apge load)
                setTheme();
              });
          },
        },
        app.translator.trans(`fof-nightmode.forum.${isLight ? 'night' : 'day'}`)
      ),
      -1
    );
  });
}
