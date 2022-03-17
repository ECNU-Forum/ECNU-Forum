import app from 'flarum/forum/app';

export default function recipientCountLabel(count, attrs = {}) {
  attrs.style = attrs.style || {};
  attrs.className = 'RecipientLabel ' + (attrs.className || '');

  var label = app.translator.trans('fof-byobu.forum.labels.recipients', { count });

  return m('span', attrs, <span className="RecipientLabel-text">{label}</span>);
}
