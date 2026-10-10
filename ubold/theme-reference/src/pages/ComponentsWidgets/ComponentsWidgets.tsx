import { useState } from 'react';
import { ChartWidgetsSection } from './ChartWidgets';
import { PageTitle } from './ComponentsWidgetsLayout';
import { MetricWidgetsSection } from './MetricWidgets';
import { OtherWidgetsSection } from './OtherWidgets';
import SweetAlertOops from '@/Components/UI/SweetAlertOops/SweetAlertOops';
import type { SweetAlertMessage } from './ComponentsWidgets.types';
import './ComponentsWidgets.css';

export default function ComponentsWidgets(): JSX.Element {
  const [sweetAlert, setSweetAlert] = useState<SweetAlertMessage | null>(null);

  return (
    <section className="componentsWidgetsPage">
      <div className="componentsWidgetsPage__container">
        <PageTitle />
        <MetricWidgetsSection />
        <ChartWidgetsSection />
        <OtherWidgetsSection onSweetAlert={setSweetAlert} />
      </div>
      {sweetAlert ? <SweetAlertOops message={sweetAlert === 'chat' ? 'Você esqueceu de digitar sua mensagem do bate-papo' : 'Você esqueceu de digitar o texto da tarefa'} onClose={() => setSweetAlert(null)} /> : null}
    </section>
  );
}
