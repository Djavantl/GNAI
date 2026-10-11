import { useState } from 'react';
import './Portlets.css';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import Portlet, { type PortletTone } from '@/components/UI/Portlet/Portlet';

interface PortletItem {
  title: string;
  tone: PortletTone;
}

const portlets: PortletItem[] = [
  { title: 'Cabeçalho padrão', tone: 'default' },
  { title: 'Cabeçalho primário', tone: 'custom' },
  { title: 'Cabeçalho primário', tone: 'primary' },
  { title: 'Cabeçalho informativo', tone: 'info' },
  { title: 'Cabeçalho de sucesso', tone: 'success' },
  { title: 'Cabeçalho de aviso', tone: 'warning' },
  { title: 'Cabeçalho de perigo', tone: 'danger' },
  { title: 'Cabeçalho inverso', tone: 'inverse' },
  { title: 'Cabeçalho rosa', tone: 'pink' },
  { title: 'Cabeçalho roxo', tone: 'purple' }
];

const bodyText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce elementum, nulla vel pellentesque consequat, ante nulla hendrerit arcu, ac tincidunt mauris lacus sed leo. vamus suscipit molestie vestibulum.';

export function PortletsShowcase(): React.JSX.Element {
  const [removedPortletKeys, setRemovedPortletKeys] = useState<Set<string>>(() => new Set());

  function handlePortletRemoved(key: string): void {
    setRemovedPortletKeys((currentKeys: Set<string>) => {
      const nextKeys = new Set(currentKeys);
      nextKeys.add(key);
      return nextKeys;
    });
  }

  return (
    <section className="portletsPage" aria-label="Portlets">
      <div className="portletsPage__pageTitleRow">
        <h4 className="page-title portletsPage__pageTitle">Portlets</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'UI Kit', href: '#' }, { label: 'Portlets' }]} />
      </div>
      <div className="portletsPage__grid">
        {portlets.map((item: PortletItem, index: number) => {
          const key = `${item.tone}-${index}`;

          if (removedPortletKeys.has(key)) {
            return null;
          }

          return (
            <div className="portletsPage__col" key={key}>
              <Portlet title={item.title} tone={item.tone} onRemoved={() => handlePortletRemoved(key)}>{bodyText}</Portlet>
            </div>
          );
        })}
      </div>
    </section>
  );
}
