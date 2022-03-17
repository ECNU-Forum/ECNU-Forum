import app from 'flarum/forum/app';
import MadePublic from './MadePublic';
import RecipientLeft from './RecipientLeft';
import RecipientsModified from './RecipientsModified';

export default () => {
  app.postComponents.recipientsModified = RecipientsModified;
  app.postComponents.recipientLeft = RecipientLeft;
  app.postComponents.madePublic = MadePublic;
};
