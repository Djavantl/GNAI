import './Panels.css';
import Panel from '@/Components/UI/Panel/Panel';
import PanelListGroup from '@/Components/UI/Panel/PanelListGroup';
import PanelTable from '@/Components/UI/Panel/PanelTable';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  borderedPanels,
  coloredPanels,
  footerPanels,
  panelsBodyText,
  panelsExampleText,
  panelsListItems,
  panelsTableHeaders,
  panelsTableRows,
  type PanelsPanelItem
} from './Panels.data';

export function renderTextPanel(item: PanelsPanelItem, bodyText: string, footer?: string): JSX.Element {
  return <Panel key={`${item.appearance}-${item.tone}-${item.title}`} title={item.title} tone={item.tone} appearance={item.appearance} body={<p>{bodyText}</p>} footer={footer} />;
}

export function PanelsShowcase(): JSX.Element {
  return (
    <section className="panelsPage" aria-label="Painéis">
      <div className="panelsPage__container">
        <div className="panelsPage__row">
          <div className="panelsPage__col panelsPage__col--full">
            <div className="panelsPage__pageTitleRow">
              <h4 className="page-title panelsPage__pageTitle">Painéis</h4>
              <PageBreadcrumb
                items={[
                  { label: 'Ubold', href: '#' },
                  { label: 'UI Kit', href: '#' },
                  { label: 'Painéis' }
                ]}
              />
            </div>
          </div>
        </div>

        <div className="panelsPage__row">
          {borderedPanels.map((item: PanelsPanelItem) => (
            <div key={`${item.appearance}-${item.tone}`} className="panelsPage__col panelsPage__col--third">
              {renderTextPanel(item, panelsBodyText)}
            </div>
          ))}
        </div>

        <div className="panelsPage__row">
          <div className="panelsPage__col panelsPage__col--full">
            <h4 className="panelsPage__sectionHeading page-header">Painéis com cor de fundo</h4>
          </div>
        </div>

        <div className="panelsPage__row">
          {coloredPanels.map((item: PanelsPanelItem) => (
            <div key={`${item.appearance}-${item.tone}`} className="panelsPage__col panelsPage__col--third">
              {renderTextPanel(item, panelsBodyText)}
            </div>
          ))}
        </div>

        <div className="panelsPage__row">
          <div className="panelsPage__col panelsPage__col--full">
            <h4 className="panelsPage__sectionHeading page-header">Painéis com rodapé</h4>
          </div>
        </div>

        <div className="panelsPage__row">
          {footerPanels.map((item: PanelsPanelItem) => (
            <div key={`${item.appearance}-${item.tone}`} className="panelsPage__col panelsPage__col--third">
              {renderTextPanel(item, panelsBodyText, 'Rodapé do painel')}
            </div>
          ))}
        </div>

        <div className="panelsPage__row">
          <div className="panelsPage__col panelsPage__col--full">
            <h4 className="panelsPage__sectionHeading page-header">Exemplos de painéis</h4>
          </div>
        </div>

        <div className="panelsPage__row">
          <div className="panelsPage__col panelsPage__col--third">
            <Panel
              title="Painel primário"
              tone="default"
              appearance="standard"
              body={<p>{panelsExampleText}</p>}
            >
              <PanelListGroup items={panelsListItems} />
            </Panel>
          </div>

          <div className="panelsPage__col panelsPage__col--third">
            <Panel
              title="Painel primário"
              tone="custom"
              appearance="color"
              body={<p>{panelsExampleText}</p>}
            >
              <PanelTable headers={panelsTableHeaders} rows={panelsTableRows} />
            </Panel>
          </div>

          <div className="panelsPage__col panelsPage__col--third">
            <Panel
              title="Painel primário"
              tone="primary"
              appearance="color"
            >
              <PanelTable headers={panelsTableHeaders} rows={panelsTableRows} />
            </Panel>
          </div>
        </div>
      </div>
    </section>
  );
}
