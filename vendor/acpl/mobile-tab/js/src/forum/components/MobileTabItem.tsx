import Component, { ComponentAttrs } from 'flarum/common/Component';
import { Children, Vnode } from 'mithril';
import LinkButton from 'flarum/common/components/LinkButton';

interface MobileTabItemAttrs extends ComponentAttrs {
  route: string;
  icon: string;
  label: string;
}

export default class MobileTabItem extends Component {
  attrs!: MobileTabItemAttrs;

  view(vnode: Vnode<MobileTabItemAttrs, this>): Children {
    const { route, icon, label } = this.attrs;
    return (
      <LinkButton href={route} icon={icon} title={label}>
        {label}
      </LinkButton>
    );
  }
}
