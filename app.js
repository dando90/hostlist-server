(() => {
  const api = (path) => `${APP_BASE}/demo-api/hosts${path}`;
  let currentPage = 1;
  const pageSize = 20;

  const node = (tag, className, value) => {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (value !== undefined) element.textContent = value;
    return element;
  };

  window.loadDirectory = async (page = 1) => {
    currentPage = Math.max(1, page);
    const tbody = document.getElementById('host-rows');
    if (!tbody) return;
    tbody.replaceChildren(Object.assign(node('tr'), { innerHTML: '<td colspan="6" class="loading">Loading profiles…</td>' }));
    try {
      const pageStart = (currentPage - 1) * pageSize + 1;
      const response = await fetch(`${api('')}?pageStart=${pageStart}&pageCount=${pageSize}`);
      if (!response.ok) throw new Error('The directory could not be loaded.');
      const data = await response.json();
      tbody.replaceChildren();
      data.records.forEach((host) => {
        const row = node('tr');
        const name = node('td');
        name.append(node('strong', 'host-name', host.fullname), node('span', 'host-id', `Host #${String(host.id).padStart(3, '0')}`));
        const company = node('td');
        company.append(node('strong', 'company-name', host.company), node('span', 'cell-note', `${host.country} · ${host.languages}`));
        const location = node('td', 'location', `${host.city}, ${host.country}`);
        const domain = node('td');
        domain.append(node('span', 'domain-pill', host.primary_domain));
        const focus = node('td', 'focus-cell', host.secondary_domain);
        const action = node('td');
        const link = node('a', 'view-link', 'View profile ↗');
        link.href = `${APP_BASE}/hosts/${host.id}`;
        link.target = '_blank';
        link.rel = 'noopener';
        action.append(link);
        row.append(name, company, location, domain, focus, action);
        tbody.append(row);
      });
      const totalPages = Math.max(1, Math.ceil(data.totalRows / pageSize));
      document.getElementById('result-count').textContent = `${data.totalRows} hosts · ${data.records.length} shown`;
      document.getElementById('page-label').textContent = `Page ${currentPage} of ${totalPages}`;
      document.getElementById('previous-page').disabled = currentPage <= 1;
      document.getElementById('next-page').disabled = currentPage >= totalPages;
    } catch (error) {
      tbody.replaceChildren();
      const row = node('tr');
      row.append(Object.assign(node('td', 'loading', error.message), { colSpan: 6 }));
      tbody.append(row);
    }
  };

  window.loadHostDetail = async (hostId) => {
    const card = document.getElementById('host-detail');
    if (!card) return;
    try {
      const response = await fetch(api(`/${hostId}`));
      if (!response.ok) throw new Error(response.status === 404 ? 'This host profile was not found.' : 'The profile could not be loaded.');
      const host = await response.json();
      card.replaceChildren();
      card.append(node('p', 'eyebrow', `${host.city}, ${host.country} · Fictional profile`));
      card.append(node('h1', '', host.fullname));
      card.append(node('p', 'detail-company', host.company));
      const badges = node('div', 'badge-row');
      [host.primary_domain, host.secondary_domain].forEach((item) => badges.append(node('span', 'domain-pill', item)));
      card.append(badges);

      const facts = node('div', 'facts-grid');
      [[`${host.host_experience_years} years`, 'Host experience'], [String(host.team_size), 'Team size'], [host.languages, 'Languages']].forEach(([value, label]) => {
        const fact = node('div', 'fact'); fact.append(node('strong', '', value), node('span', '', label)); facts.append(fact);
      });
      card.append(facts);
      const sections = [
        ['What we are building', host.offer_extra_info],
        ['Skills and tools', host.specific_skills],
        ['Why we share our work', host.motivation],
      ];
      sections.forEach(([heading, text]) => {
        const section = node('section', 'detail-section');
        section.append(node('h2', '', heading), node('p', '', text));
        card.append(section);
      });
      const impact = node('section', 'detail-section');
      impact.append(node('h2', '', 'Potential impact'));
      const list = node('ul', 'impact-list');
      host.business_impact.forEach((item) => list.append(node('li', '', item)));
      impact.append(list);
      card.append(impact);
      card.append(node('p', 'synthetic-note', 'This person, company and opportunity are entirely fictional and were generated for demonstration.'));
    } catch (error) {
      card.replaceChildren(node('p', 'eyebrow', 'Fictional IT host'), node('h1', '', 'Profile unavailable'), node('p', 'detail-copy', error.message));
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('previous-page')?.addEventListener('click', () => window.loadDirectory(currentPage - 1));
    document.getElementById('next-page')?.addEventListener('click', () => window.loadDirectory(currentPage + 1));
  });
})();
