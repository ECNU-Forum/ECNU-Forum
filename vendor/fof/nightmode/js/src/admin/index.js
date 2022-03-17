import app from 'flarum/admin/app';
import setSelectedTheme from '../common/setSelectedTheme';
import Themes from '../common/Themes';

function populateThemes() {
  let options = {};

  // add themes based on JS enum
  Object.keys(Themes).forEach((theme, i) => {
    if (theme === 'DEFAULT') return;

    options[i] = app.translator.trans(`fof-nightmode.admin.settings.modal.theme_${theme.toLowerCase()}`);
  });

  return options;
}

app.initializers.add('fof-nightmode', () => {
  app.extensionData
    .for('fof-nightmode')
    .registerSetting({
      label: app.translator.trans('fof-nightmode.admin.settings.modal.default_theme'),
      setting: 'fof-nightmode.default_theme',
      type: 'select',
      options: populateThemes(),
    })
    .registerSetting({
      label: app.translator.trans('fof-nightmode.admin.settings.modal.default_theme_helper'),
      type: 'hidden',
    });

  setSelectedTheme();
});
