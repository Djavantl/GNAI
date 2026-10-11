import Table from '@/components/Tables/Table/Table';
import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { sweetAlertExamples } from './ComponentsSweetAlert.data';
import type { SweetAlertExample, SweetAlertKind } from './ComponentsSweetAlert.types';

interface ExamplesSectionProps {
  onOpenExample: (exampleId: SweetAlertKind) => void;
}

export function PageTitle(): React.JSX.Element {
  return (
    <div className="componentsSweetAlertPage__pageTitleRow">
      <div className="componentsSweetAlertPage__col componentsSweetAlertPage__col--full">
        <h4 className="page-title componentsSweetAlertPage__pageTitle">Sweet-Alert</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Sweet-Alert' }]} />
      </div>
    </div>
  );
}

function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return (
    <h4 className="componentsSweetAlertPage__sectionTitle header-title m-t-0">
      <b>{children}</b>
    </h4>
  );
}

function Description({ children }: { children: ReactNode }): React.JSX.Element {
  return <p className="componentsSweetAlertPage__description text-muted m-b-30 font-13">{children}</p>;
}

export function SweetAlertExamplesSection({ onOpenExample }: ExamplesSectionProps): React.JSX.Element {
  return (
    <div className="componentsSweetAlertPage__row">
      <div className="componentsSweetAlertPage__col componentsSweetAlertPage__col--full">
        <CardBox className="componentsSweetAlertPage__card">
          <SectionTitle>Exemplos de Sweet-Alert</SectionTitle>
          <Description>Uma bela substituição para o “Alert” do JavaScript.</Description>

          <Table appearance="inherit" className="componentsSweetAlertPage__table table">
            <thead>
              <tr>
                <th style={{ width: '50%' }}>Tipo de alerta</th>
                <th>Exemplo</th>
              </tr>
            </thead>
            <tbody>
              {sweetAlertExamples.map((example: SweetAlertExample) => (
                <tr key={example.id}>
                  <td className="middle-align">{example.label}</td>
                  <td>
                    <button
                      type="button"
                      className="btn btn-info waves-effect waves-light btn-sm"
                      onClick={() => onOpenExample(example.id)}
                    >
                      {example.buttonLabel}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </Table>
        </CardBox>
      </div>
    </div>
  );
}
