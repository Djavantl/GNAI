import type { ReactNode } from 'react';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import ProgressBar, { type ProgressBarOrientation } from '@/Components/UI/ProgressBar/ProgressBar';
import {
  activeProgressBars,
  basicProgressBars,
  entranceProgressBars,
  entranceStripedProgressBars,
  labeledProgressBars,
  sizedProgressBars,
  stackedProgressSegments,
  stripedProgressBars,
  verticalProgressBars,
  type ProgressDemoItem
} from './ProgressBars.data';

interface SectionProps {
  title: string;
  description: ReactNode;
  children: ReactNode;
}

function Section({ title, description, children }: SectionProps): JSX.Element {
  return (
    <div className="progressBarsPage__section">
      <h4 className="progressBarsPage__sectionTitle header-title m-t-0">
        <b>{title}</b>
      </h4>
      <p className="progressBarsPage__description text-muted m-b-30 font-13">{description}</p>
      {children}
    </div>
  );
}

function renderProgressBars(items: ProgressDemoItem[], orientation: ProgressBarOrientation = 'horizontal'): JSX.Element[] {
  return items.map((item: ProgressDemoItem, index: number) => (
    <ProgressBar
      key={`${orientation}-${item.tone}-${item.value}-${index}`}
      value={item.value}
      tone={item.tone}
      size={item.size}
      striped={item.striped}
      active={item.active}
      animated={item.animated}
      label={item.label}
      orientation={orientation}
      className={index === items.length - 1 ? 'progressBarsPage__lastProgress' : undefined}
    />
  ));
}

export function ProgressBarsPageTitle(): JSX.Element {
  return (
    <div className="progressBarsPage__pageTitleRow">
      <h4 className="page-title progressBarsPage__pageTitle">Barras de progresso</h4>
      <PageBreadcrumb
        items={[
          { label: 'Ubold', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Barras de progresso' }
        ]}
      />
    </div>
  );
}

export function BasicAndStripedProgressSection(): JSX.Element {
  return (
    <CardBox className="progressBarsPage__card">
      <div className="progressBarsPage__row">
        <div className="progressBarsPage__col">
          <Section title="Exemplo básico" description="Barra de progresso padrão.">
            {renderProgressBars(basicProgressBars)}
          </Section>
        </div>

        <div className="progressBarsPage__col progressBarsPage__col--offset">
          <Section
            title="Exemplo listrado"
            description={
              <>
                Usa um gradiente para criar um efeito listrado. Use a classe <code>.progress-bar-striped</code>.
              </>
            }
          >
            {renderProgressBars(stripedProgressBars)}
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

export function SizedAndActiveProgressSection(): JSX.Element {
  return (
    <CardBox className="progressBarsPage__card">
      <div className="progressBarsPage__row">
        <div className="progressBarsPage__col">
          <Section
            title="Exemplo de tamanhos"
            description={
              <>
                Use as classes <code>.progress-sm</code>, <code>.progress-md</code> e <code>.progress-lg</code>.
              </>
            }
          >
            {renderProgressBars(sizedProgressBars)}
          </Section>
        </div>

        <div className="progressBarsPage__col progressBarsPage__col--offset">
          <Section
            title="Exemplo animado"
            description={
              <>
                Adicione <code>.active</code> em <code>.progress-bar-striped</code> para animar as listras da direita para a esquerda.
              </>
            }
          >
            {renderProgressBars(activeProgressBars)}
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

export function LabeledAndStackedProgressSection(): JSX.Element {
  return (
    <CardBox className="progressBarsPage__card">
      <div className="progressBarsPage__row">
        <div className="progressBarsPage__col">
          <Section
            title="Exemplo concluído"
            description={
              <>
                Remova o <code>&lt;span&gt;</code> com a classe <code>.sr-only</code> dentro da barra para mostrar uma porcentagem visível.
              </>
            }
          >
            {renderProgressBars(labeledProgressBars)}
          </Section>
        </div>

        <div className="progressBarsPage__col progressBarsPage__col--offset">
          <Section
            title="Exemplo empilhado"
            description={
              <>
                Coloque várias barras dentro do mesmo <code>.progress</code> para empilhá-las.
              </>
            }
          >
            <ProgressBar segments={stackedProgressSegments} className="progressBarsPage__lastProgress" />
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

export function EntranceProgressSection(): JSX.Element {
  return (
    <CardBox className="progressBarsPage__card">
      <div className="progressBarsPage__row">
        <div className="progressBarsPage__col">
          <Section
            title="Exemplo animado"
            description={
              <>
                Use as classes <code>.wow</code>, <code>.animated</code> e <code>.progress-animated</code>.
              </>
            }
          >
            {renderProgressBars(entranceProgressBars)}
          </Section>
        </div>

        <div className="progressBarsPage__col progressBarsPage__col--offset">
          <Section
            title="Exemplo listrado animado"
            description={
              <>
                Use as classes <code>.wow</code>, <code>.animated</code>, <code>.progress-bar-striped</code> e <code>.progress-animated</code>.
              </>
            }
          >
            {renderProgressBars(entranceStripedProgressBars)}
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

export function VerticalProgressSection(): JSX.Element {
  return (
    <CardBox className="progressBarsPage__card">
      <div className="progressBarsPage__row">
        <div className="progressBarsPage__col">
          <Section
            title="Barra de progresso vertical"
            description={
              <>
                Use a classe <code>.progress-vertical</code>.
              </>
            }
          >
            {renderProgressBars(verticalProgressBars, 'vertical')}
          </Section>
        </div>

        <div className="progressBarsPage__col progressBarsPage__col--offset">
          <Section
            title="Barra de progresso vertical inferior"
            description={
              <>
                Use a classe <code>.progress-vertical-bottom</code>.
              </>
            }
          >
            {renderProgressBars(verticalProgressBars, 'vertical-bottom')}
          </Section>
        </div>
      </div>
    </CardBox>
  );
}
