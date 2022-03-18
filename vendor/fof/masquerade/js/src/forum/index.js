import app from 'flarum/forum/app';
import User from 'flarum/common/models/User';
import Field from './../lib/models/Field';
import Answer from './../lib/models/Answer';
import Model from 'flarum/common/Model';

import addProfilePane from './addProfilePane';
import mutateUserHero from './mutateUserHero';

// Exports
import ProfileConfigurePane from './panes/ProfileConfigurePane';
import ProfilePane from './panes/ProfilePane';
import RootMasqueradePane from './panes/RootMasqueradePane';
import BaseField from './types/BaseField';
import BooleanField from './types/BooleanField';
import EmailField from './types/EmailField';
import SelectField from './types/SelectField';
import TypeFactory from './types/TypeFactory';
import UrlField from './types/UrlField';

app.initializers.add('fof-masquerade', (app) => {
  app.store.models['masquerade-field'] = Field;
  app.store.models['masquerade-answer'] = Answer;

  User.prototype.bioFields = Model.hasMany('bioFields');
  User.prototype.masqueradeAnswers = Model.hasMany('masqueradeAnswers');
  User.prototype.canEditMasqueradeProfile = Model.attribute('canEditMasqueradeProfile');

  addProfilePane();

  mutateUserHero();
});

const components = {
  ProfileConfigurePane,
  ProfilePane,
  RootMasqueradePane,
};

const types = {
  BaseField,
  BooleanField,
  EmailField,
  SelectField,
  TypeFactory,
  UrlField,
};

export { components, types };
