import { type ReactNode } from 'react';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PeityChart from '@/Components/UI/PeityChart/PeityChart';
import SparklineChart, { type SparklineChartConfig } from '@/Components/UI/SparklineChart/SparklineChart';
import { joinClassNames } from '@/utils/joinClassNames';
import KnobChart from '@/Components/UI/KnobChart/KnobChart';
import { sparklineWidgetCharts, sparklineWidgetChartsSecondary } from './ComponentsWidgets.data';
import { Col, Row, SectionHeader } from './ComponentsWidgetsLayout';
import type { Tone } from './ComponentsWidgets.types';

export function BarWidget({ chart, tall = false, tone }: { chart: ReactNode; tall?: boolean; tone: Tone }): JSX.Element {
  return (
    <CardBox className={joinClassNames('componentsWidgetsPage__barCard', tall ? 'componentsWidgetsPage__barCard--tall' : undefined)}>
      <div className="bar-widget">
        <div className="table-box">
          <div className="table-detail">
            <div className={joinClassNames('iconbox', `bg-${tone}`)}><i className="icon-layers" /></div>
          </div>
          <div className="table-detail">
            <h4 className="m-t-0 m-b-5"><b>1256</b></h4>
            <h5 className="text-muted m-b-0 m-t-0">Visitantes</h5>
          </div>
          <div className="table-detail text-right">{chart}</div>
        </div>
      </div>
    </CardBox>
  );
}

function ChartStats(): JSX.Element {
  return (
    <ul className="list-inline m-t-15 componentsWidgetsPage__chartStats">
      {['Meta', 'Semana passada', 'Mês passado'].map((label, index) => (
        <li key={label}>
          <h5 className="text-muted m-t-20">{label}</h5>
          <h4 className="m-b-0">{index === 0 ? '$1000' : index === 1 ? '$523' : '$965'}</h4>
        </li>
      ))}
    </ul>
  );
}

export function KnobRevenueCard({ color, value }: { color: string; value: number }): JSX.Element {
  return (
    <CardBox className="componentsWidgetsPage__chartCard componentsWidgetsPage__chartCard--knob">
      <h4 className="text-dark header-title m-t-0 m-b-30">Receita total</h4>
      <div className="widget-chart text-center">
        <KnobChart value={value} options={{ width:150, height:150, lineCap:'round', fgColor:color, skin:'tron', angleOffset:180, readOnly:true, thickness:.15 }} />
        <h5 className="text-muted m-t-20">Total de vendas feitas hoje</h5>
        <h2 className="font-600">$75</h2>
        <ChartStats />
      </div>
    </CardBox>
  );
}

export function SparkChartCard({ chart, children }: { chart?: SparklineChartConfig; children?: ReactNode }): JSX.Element {
  return (
    <CardBox className="componentsWidgetsPage__chartCard componentsWidgetsPage__chartCard--spark">
      <h4 className="text-dark header-title m-t-0 m-b-30">Receita total</h4>
      <div className="widget-chart text-center">
        {chart ? <SparklineChart chart={chart} className="componentsWidgetsPage__sparklineHost" /> : <div className="componentsWidgetsPage__sparklineHost">{children}</div>}
        <ChartStats />
      </div>
    </CardBox>
  );
}

export function ChartWidgetsSection(): JSX.Element {
  return (
    <>
      <SectionHeader>Widget de gráficos</SectionHeader>
      <Row>
        <Col size="third"><BarWidget tone="info" chart={<PeityChart type="bar" values="5,3,9,6,5,9,7,3,5,2,9,7,2,1" options={{ fill: ['#34d3eb', '#ebeff2'], width: '120', height: '45' }} />} /></Col>
        <Col size="third"><BarWidget tone="custom" chart={<PeityChart type="pie" values="1/5" options={{ fill: ['#5fbeaa', '#ebeff2'], width: '50', height: '45' }} />} /></Col>
        <Col size="third"><BarWidget tone="danger" chart={<PeityChart type="donut" values="1/5" options={{ fill: ['#f05050', '#ebeff2'], width: '50', height: '45' }} />} /></Col>
      </Row>
      <Row>
        <Col size="third"><BarWidget tone="purple" tall chart={<PeityChart type="donut" values="5/7" options={{ fill: ['#7266ba', '#ebeff2'], innerRadius: 20, radius: 24 }} />} /></Col>
        <Col size="third"><BarWidget tone="inverse" tall chart={<PeityChart type="line" values="5,3,9,6,5,9,7,3,5,2" options={{ fill: '#4c5667', stroke: '#4c5667', width: '100', height: '48' }} />} /></Col>
        <Col size="third"><BarWidget tone="warning" tall chart={<PeityChart type="bar" values="5,3,9,6,5,9,7,3,5,2,9,7,2,1" options={{ fill: ['#ffbd4a', '#ebeff2'], width: '120', height: '48' }} />} /></Col>
      </Row>
      <Row>
        <Col size="third"><KnobRevenueCard color="#5d9cec" value={55} /></Col>
        <Col size="third"><KnobRevenueCard color="#fb6d9d" value={80} /></Col>
        <Col size="third"><KnobRevenueCard color="#34d3eb" value={60} /></Col>
      </Row>
      <Row>
        {sparklineWidgetCharts.map((chart, index) => (
          <Col key={`sparkline-widget-${index}`} size="third"><SparkChartCard chart={chart} /></Col>
        ))}
      </Row>
      <Row>
        {sparklineWidgetChartsSecondary.map((chart, index) => (
          <Col key={`sparkline-widget-secondary-${index}`} size="third"><SparkChartCard chart={chart} /></Col>
        ))}
      </Row>
    </>
  );
}
