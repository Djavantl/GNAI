import CardBox from '@/Components/UI/CardBox/CardBox';
import ProgressBar from '@/Components/UI/ProgressBar/ProgressBar';
import { joinClassNames } from '@/utils/joinClassNames';
import AnimatedCounter from './AnimatedCounter';
import { Col, Row, ToneIcon } from './ComponentsWidgetsLayout';
import { iconMetricItems, inlineMetrics, miniStats, panelMetrics, statusMetrics } from './ComponentsWidgets.data';
import type { IconMetricItem, MiniStatItem, PanelMetricItem, StatusMetricItem } from './ComponentsWidgets.types';

export function IconMetricCard({ iconClass, tone }: IconMetricItem): JSX.Element {
  return (
    <CardBox className="widget-bg-color-icon componentsWidgetsPage__iconMetricCard clearfix">
      <div className={joinClassNames('bg-icon pull-left', `bg-icon-${tone}`)}>
        <ToneIcon className={iconClass} tone={tone} />
      </div>
      <div className="text-right">
        <h3 className="text-dark"><AnimatedCounter value={31570} className="counter" /></h3>
        <p className="text-muted">Status da receita</p>
      </div>
      <div className="clearfix" />
    </CardBox>
  );
}

export function InlineMetrics(): JSX.Element {
  return (
    <CardBox className="widget-inline componentsWidgetsPage__inlineCard">
      <Row>
        {inlineMetrics.map((item, index) => (
          <Col key={item.label} size="quarter">
            <div className={joinClassNames('widget-inline-box text-center', index === inlineMetrics.length - 1 ? 'b-0' : undefined)}>
              <h3><ToneIcon className={item.iconClass} tone={item.tone} /> <b>{item.value}</b></h3>
              <h4 className="text-muted">{item.label}</h4>
            </div>
          </Col>
        ))}
      </Row>
    </CardBox>
  );
}

function StatusInfoTooltip(): JSX.Element {
  return (
    <span className="componentsWidgetsPage__statusTooltipHost pull-right" tabIndex={0} aria-label="Tooltip à direita">
      <i className="fa fa-info-circle text-muted inform" />
      <span className="tooltip fade bottom in componentsWidgetsPage__statusTooltip" role="tooltip">
        <span className="tooltip-arrow" />
        <span className="tooltip-inner">Tooltip à direita</span>
      </span>
    </span>
  );
}

export function StatusCard({ description, title, tone, trend, trendIcon, trendTone, value }: StatusMetricItem): JSX.Element {
  return (
    <CardBox className="widget-box-1 bg-white componentsWidgetsPage__statusCard">
      <StatusInfoTooltip />
      <h4 className="text-dark">{title}</h4>
      <h2 className={joinClassNames(`text-${tone}`, 'text-center')}>{value}</h2>
      <p className="text-muted">
        {description}
        <span className="pull-right"><i className={joinClassNames(trendIcon, `text-${trendTone}`, 'm-r-5')} />{trend}</span>
      </p>
    </CardBox>
  );
}

export function MiniStat({ iconClass, label, target, tone, value }: MiniStatItem): JSX.Element {
  return (
    <CardBox className="mini-stat clearfix componentsWidgetsPage__miniStatCard">
      <span className={joinClassNames('mini-stat-icon', `bg-${tone}`)}><i className={joinClassNames(iconClass, 'text-white')} /></span>
      <div className="mini-stat-info text-right text-dark">
        <AnimatedCounter className="counter text-dark" value={value} wrapper="span" />
        {label}
      </div>
      <div className="tiles-progress">
        <div className="m-t-20">
          <h5 className="text-uppercase">Meta <span className="pull-right">{target}%</span></h5>
          <ProgressBar value={target} tone={tone} size="sm" className="m-0 componentsWidgetsPage__miniProgress" />
        </div>
      </div>
    </CardBox>
  );
}

export function PanelMetric({ iconClass, label, tone, value }: PanelMetricItem): JSX.Element {
  return (
    <div className="widget-panel widget-style-2 bg-white componentsWidgetsPage__panelMetric">
      <ToneIcon className={iconClass} tone={tone} />
      <h2 className="m-0 text-dark counter font-600"><AnimatedCounter value={value} wrapper="span" /></h2>
      <div className="text-muted m-t-5">{label}</div>
    </div>
  );
}

export function MetricWidgetsSection(): JSX.Element {
  return (
    <>
      <Row>{iconMetricItems.slice(0, 4).map((item) => <Col key={item.tone} size="quarter"><IconMetricCard {...item} /></Col>)}</Row>
      <Row>{iconMetricItems.slice(4).map((item) => <Col key={item.tone} size="quarter"><IconMetricCard {...item} /></Col>)}</Row>
      <Row><Col size="full"><InlineMetrics /></Col></Row>
      <Row>{statusMetrics.map((item) => <Col key={`${item.title}-${item.value}`} size="quarter"><StatusCard {...item} /></Col>)}</Row>
      <Row>{miniStats.map((item) => <Col key={item.label} size="quarter"><MiniStat {...item} /></Col>)}</Row>
      <Row>{panelMetrics.map((item) => <Col key={item.label} size="quarter"><PanelMetric {...item} /></Col>)}</Row>
    </>
  );
}
