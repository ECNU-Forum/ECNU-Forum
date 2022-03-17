import Group from 'flarum/common/models/Group';
import User from 'flarum/common/models/User';
import classList from 'flarum/common/utils/classList';
import RecipientLabel from './RecipientLabel';

export default function recipientsLabel(recipients?: (User | Group)[], attrs: Record<string, unknown> = {}): JSX.Element {
  const { link, className, ...otherAttrs } = attrs;

  otherAttrs.className = classList('RecipientsLabel', className);

  return (
    <span {...otherAttrs}>
      {recipients?.map((recipient, index) => {
        const type = recipient instanceof User ? 'u' : 'g';

        return <RecipientLabel key={`${type}-${index}`} recipient={recipient} link={link} />;
      })}
      {!recipients && <RecipientLabel />}
    </span>
  );
}
