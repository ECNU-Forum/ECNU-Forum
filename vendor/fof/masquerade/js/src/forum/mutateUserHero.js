import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import UserCard from 'flarum/forum/components/UserCard';
import TypeFactory from './types/TypeFactory';

export default function mutateUserHero() {
  extend(UserCard.prototype, 'infoItems', function (items) {
    const answers = app.forum.attribute('canViewMasquerade') ? this.attrs.user.bioFields() || [] : [];

    items.add(
      'masquerade-bio',
      <div>
        {answers.map((answer) => {
          const field = answer.attribute('field');
          const type = TypeFactory.typeForField({
            field,
            value: answer.content(),
          });

          return type.answerField();
        })}
      </div>
    );
  });
}
