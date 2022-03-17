import app from 'flarum/forum/app';
import IndexPage from 'flarum/forum/components/IndexPage';
import PrivateDiscussionsUserPage from './PrivateDiscussionsUserPage';

export default () => {
  app.routes.byobuUserPrivate = { path: '/u/:username/private', component: PrivateDiscussionsUserPage };
  app.routes.byobuPrivate = { path: '/private', component: IndexPage };
};
