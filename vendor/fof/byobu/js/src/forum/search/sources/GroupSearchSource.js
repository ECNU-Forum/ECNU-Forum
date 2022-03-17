import app from 'flarum/forum/app';
import highlight from 'flarum/common/helpers/highlight';

export default class GroupSearchSource {
  search(query) {
    return app.store.find('groups', {
      filter: { q: query },
      page: { limit: 5 },
    });
  }

  view(query) {
    query = query.toLowerCase();

    const results = app.store.all('groups').filter((group) => group.namePlural().toLowerCase().substr(0, query.length) === query);

    if (!results.length) return '';

    return [
      <li className="Dropdown-header">{app.translator.trans('fof-byobu.forum.search.headings.groups')}</li>,
      results.map((group) => {
        const groupName = group.namePlural();
        var name = highlight(groupName, query);

        return (
          <li className="SearchResult" data-index={'groups:' + group.id()}>
            <a data-index={'groups:' + group.id()}>
              <span class="groupName">{name}</span>
            </a>
          </li>
        );
      }),
    ];
  }
}
