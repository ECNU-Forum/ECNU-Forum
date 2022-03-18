import Model from 'flarum/common/Model';

export default class Field extends Model {
  name = Model.attribute('name');
  description = Model.attribute('description');
  type = Model.attribute('type');
  validation = Model.attribute('validation');
  required = Model.attribute('required');
  prefix = Model.attribute('prefix');
  icon = Model.attribute('icon');
  sort = Model.attribute('sort');
  deleted_at = Model.attribute('deleted_at', Model.transformDate);
  answer = Model.hasOne('answer');
  on_bio = Model.attribute('on_bio');

  apiEndpoint() {
    return '/masquerade/fields' + (this.exists ? '/' + this.data.id : '');
  }
}
