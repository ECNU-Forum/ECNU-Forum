import app from 'flarum/forum/app';
import addSettingsItems from './addSettingsItems';
import setSelectedTheme from '../common/setSelectedTheme';

app.initializers.add('fof-nightmode', () => {
  addSettingsItems();
  setSelectedTheme();
});
