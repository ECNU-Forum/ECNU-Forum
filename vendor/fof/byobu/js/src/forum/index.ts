import app from 'flarum/forum/app';
import events from './events';
import extend from './extend';
import pages from './pages';
import notifications from './notifications';

export * from './modals';
export * from './pages/discussions';

app.initializers.add('fof-byobu', function () {
  events();
  extend();

  pages();
  notifications();
});
