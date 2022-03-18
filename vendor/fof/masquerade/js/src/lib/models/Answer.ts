import app from 'flarum/common/app';
import Model from 'flarum/common/Model';
import computed from 'flarum/common/utils/computed';

import type Field from './Field';

export default class Answer extends Model {
  content = Model.attribute('content');
  fieldId = Model.attribute('fieldId');
  field = computed<Field>('fieldId', (fieldId: string) => {
    return app.store.getById('masquerade-field', fieldId);
  });
  userId = Model.attribute('user_id');
}
