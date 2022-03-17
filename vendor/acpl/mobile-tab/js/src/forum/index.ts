import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Application from 'flarum/common/Application';

import MobileTab from './components/MobileTab';

app.initializers.add('acpl/mobile-tab', () => {
  extend(Application.prototype, 'mount', () => {
    const mTab = document.createElement('div');
    m.mount(document.body.appendChild(mTab), MobileTab);
  });
});

export * from './components';
