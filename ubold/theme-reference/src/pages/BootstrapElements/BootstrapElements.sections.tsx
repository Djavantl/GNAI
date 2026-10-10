import PaginationList from '@/Components/Navigation/Pagination/Pagination';
import { type CSSProperties, useCallback, useEffect, useId, useLayoutEffect, useRef, useState } from 'react';
import { createPortal } from 'react-dom';
import Badge from '@/Components/UI/Badge/Badge';
import Alert from '@/Components/UI/InlineAlert/InlineAlert';
import type { AlertVariant } from '@/Components/UI/InlineAlert/InlineAlert.types';
import Button from '@/Components/UI/Button/Button';
import CardBox from '@/Components/UI/CardBox/CardBox';
import Label from '@/Components/UI/Label/Label';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { joinClassNames } from '@/utils/joinClassNames';
import './BootstrapElements.css';

type Placement = 'top' | 'right' | 'bottom' | 'left';


type Tone = 'default' | 'primary' | 'success' | 'info' | 'warning' | 'danger' | 'purple' | 'inverse' | 'pink';

const toneItems: Array<{ label: string; tone: Tone }> = [
  { label: 'Padrão', tone: 'default' },
  { label: 'Primário', tone: 'primary' },
  { label: 'Sucesso', tone: 'success' },
  { label: 'Info', tone: 'info' },
  { label: 'Aviso', tone: 'warning' },
  { label: 'Perigo', tone: 'danger' },
  { label: 'Roxo', tone: 'purple' },
  { label: 'Inverso', tone: 'inverse' },
  { label: 'Rosa', tone: 'pink' }
];

const badgeItems: Array<{ label: string; tone: Tone | 'default' }> = [
  { label: '15', tone: 'primary' },
  { label: '0', tone: 'success' },
  { label: '21', tone: 'info' },
  { label: '3', tone: 'inverse' },
  { label: '35', tone: 'warning' },
  { label: '32', tone: 'danger' },
  { label: '51', tone: 'purple' },
  { label: '77', tone: 'pink' },
  { label: '9', tone: 'default' }
];



const alertCopy: Record<AlertVariant, { lead: string; text: string }> = {
  success: { lead: 'Muito bem!', text: 'Você leu esta mensagem de alerta importante com sucesso.' },
  info: { lead: 'Atenção!', text: 'Este alerta precisa da sua atenção, mas não é tão importante.' },
  warning: { lead: 'Aviso!', text: 'É melhor verificar, algo não parece muito bom.' },
  danger: { lead: 'Ops!', text: 'Altere alguns pontos e tente enviar novamente.' }
};

function SectionTitle({ title }: { title: string }): JSX.Element {
  return (
    <h4 className="bootstrapElementsPage__sectionTitle header-title m-t-0">
      <b>{title}</b>
    </h4>
  );
}

function Description({ children, tall = false }: { children: React.ReactNode; tall?: boolean }): JSX.Element {
  return <p className={joinClassNames('bootstrapElementsPage__description text-muted font-13', tall ? 'bootstrapElementsPage__description--tall' : undefined)}>{children}</p>;
}

function PreventLink({ children, className }: { children: React.ReactNode; className?: string }): JSX.Element {
  return (
    <a href="#" className={className} onClick={(event: React.MouseEvent<HTMLAnchorElement>) => event.preventDefault()}>
      {children}
    </a>
  );
}

export function BootstrapOverlayButton({
  children,
  content,
  dismissible = false,
  placement,
  title,
  type
}: {
  children: React.ReactNode;
  content: string;
  dismissible?: boolean;
  placement: Placement;
  title?: string;
  type: 'popover' | 'tooltip';
}): JSX.Element {
  const id = useId();
  const triggerRef = useRef<HTMLSpanElement | null>(null);
  const [open, setOpen] = useState<boolean>(false);
  const [overlayStyle, setOverlayStyle] = useState<CSSProperties>({});
  const isTooltip = type === 'tooltip';

  const updateOverlayPosition = useCallback((): void => {
    const trigger = triggerRef.current;

    if (!trigger) {
      return;
    }

    const rect = trigger.getBoundingClientRect();
    const centerX = rect.left + window.scrollX + rect.width / 2;
    const centerY = rect.top + window.scrollY + rect.height / 2;

    if (placement === 'top') {
      setOverlayStyle({ top: rect.top + window.scrollY - 10, left: centerX });
      return;
    }

    if (placement === 'bottom') {
      setOverlayStyle({ top: rect.bottom + window.scrollY + 10, left: centerX });
      return;
    }

    if (placement === 'right') {
      setOverlayStyle({ top: centerY, left: rect.right + window.scrollX + 10 });
      return;
    }

    setOverlayStyle({ top: centerY, left: rect.left + window.scrollX - 10 });
  }, [placement]);

  function openOverlay(): void {
    updateOverlayPosition();
    setOpen(true);
  }

  useLayoutEffect(() => {
    if (!open) {
      return undefined;
    }

    updateOverlayPosition();

    window.addEventListener('resize', updateOverlayPosition);
    window.addEventListener('scroll', updateOverlayPosition, true);

    return () => {
      window.removeEventListener('resize', updateOverlayPosition);
      window.removeEventListener('scroll', updateOverlayPosition, true);
    };
  }, [open, updateOverlayPosition]);

  const overlay = open ? (
    <span
      id={id}
      role={isTooltip ? 'tooltip' : 'dialog'}
      className={joinClassNames(
        isTooltip ? 'bootstrapElementsPage__tooltip' : 'bootstrapElementsPage__popover',
        'bootstrapElementsPage__overlay--portal',
        `bootstrapElementsPage__overlay--${placement}`,
        isTooltip ? 'in' : undefined
      )}
      style={overlayStyle}
    >
      {isTooltip ? (
        <span className="bootstrapElementsPage__tooltipInner">{content}</span>
      ) : (
        <>
          {title ? <span className="bootstrapElementsPage__popoverTitle">{title}</span> : null}
          <span className="bootstrapElementsPage__popoverContent">{content}</span>
        </>
      )}
      <span className="bootstrapElementsPage__overlayArrow" />
    </span>
  ) : null;

  return (
    <span
      ref={triggerRef}
      className="bootstrapElementsPage__overlayTrigger"
      onMouseEnter={() => isTooltip && openOverlay()}
      onMouseLeave={() => isTooltip && setOpen(false)}
      onFocus={() => (isTooltip || dismissible) && openOverlay()}
      onBlur={() => dismissible || isTooltip ? setOpen(false) : undefined}
    >
      <Button
        tone="default"
        waves={false}
        aria-label={typeof children === 'string' ? children : undefined}
        onClick={() => {
          if (isTooltip) {
            return;
          }

          if (dismissible) {
            openOverlay();
            return;
          }

          if (open) {
            setOpen(false);
            return;
          }

          openOverlay();
        }}
      >
        {children}
      </Button>
      {overlay ? createPortal(overlay, document.body) : null}
    </span>
  );
}

export function StandardAlerts(): JSX.Element {
  return (
    <>
      {(Object.keys(alertCopy) as AlertVariant[]).map((variant: AlertVariant) => (
        <Alert key={variant} variant={variant}>
          <strong>{alertCopy[variant].lead}</strong> {alertCopy[variant].text}
        </Alert>
      ))}
      <Alert variant="success" fade className="inlineAlert--last">
        <h4>Grande alerta!</h4>
        <p>
          Texto de exemplo para demonstrar um alerta maior. Ele mantém o bloco com conteúdo suficiente para preservar a composição visual usada no modelo legado.
        </p>
        <p>
          <Button tone="default">Quero fazer isso</Button>{' '}
          <Button tone="white">Ou fazer isso</Button>
        </p>
      </Alert>
    </>
  );
}

export function DismissableAlerts(): JSX.Element {
  return (
    <>
      {(Object.keys(alertCopy) as AlertVariant[]).map((variant: AlertVariant) => (
        <Alert key={variant} variant={variant} dismissible>
          Texto de exemplo para este alerta. <PreventLink className="inlineAlertLink">Link do alerta</PreventLink>.
        </Alert>
      ))}
      <Alert variant="info" closeButton fade className="inlineAlert--last">
        <h4>Grande alerta!</h4>
        <p>Texto de exemplo para demonstrar um alerta maior. Ele mantém o bloco com conteúdo suficiente para preservar a composição visual usada no modelo legado.</p>
        <p>
          <Button tone="info">Quero fazer isso</Button>{' '}
          <Button tone="white">Ou fazer isso</Button>
        </p>
      </Alert>
    </>
  );
}

export { default as PaginationList } from '@/Components/Navigation/Pagination/Pagination';

export function Pager({ aligned = false, disabled = false }: { aligned?: boolean; disabled?: boolean }): JSX.Element {
  return (
    <ul className="bootstrapElementsPage__pager">
      <li className={joinClassNames(aligned || disabled ? 'previous' : undefined, disabled ? 'disabled' : undefined)}>
        <PreventLink>{aligned || disabled ? '← Mais antigo' : <><i className="fa fa-angle-left" /> Anterior</>}</PreventLink>
      </li>
      <li className={aligned || disabled ? 'next' : undefined}>
        <PreventLink>{aligned || disabled ? 'Mais novo →' : <>Próximo <i className="fa fa-angle-right" /></>}</PreventLink>
      </li>
    </ul>
  );
}

export function SimpleListGroup(): JSX.Element {
  const items = [
    { text: 'Cras justo odio', badge: '14', tone: 'primary' },
    { text: 'Dapibus ac facilisis in', badge: '2', tone: 'purple' },
    { text: 'Morbi leo risus', badge: '9', tone: 'pink' },
    { text: 'Morbi leo risus', badge: '7', tone: 'inverse' },
    { text: 'Morbi leo risus', badge: '1', tone: 'success' }
  ] as const;

  return (
    <ul className="bootstrapElementsPage__listGroup">
      {items.map((item) => (
        <li key={`${item.text}-${item.badge}`} className="bootstrapElementsPage__listGroupItem">
          <Badge variant={item.tone}>{item.badge}</Badge>
          {item.text}
        </li>
      ))}
    </ul>
  );
}

export function LinkedListGroup(): JSX.Element {
  const items = [
    { text: 'Cras justo odio', active: true },
    { text: 'Dapibus ac facilisis in' },
    { text: 'Morbi leo risus' },
    { text: 'Porta ac consectetur ac', disabled: true },
    { text: 'Vestibulum at eros' }
  ];

  return (
    <div className="bootstrapElementsPage__listGroup">
      {items.map((item) => (
        <PreventLink key={item.text} className={joinClassNames('bootstrapElementsPage__listGroupItem', item.active ? 'active' : undefined, item.disabled ? 'disabled' : undefined)}>
          {item.text}
        </PreventLink>
      ))}
    </div>
  );
}

export function DescriptionListGroup(): JSX.Element {
  const items = [
    {
      title: 'Doméstico confinado por qualquer filho',
      text: 'Considere falar comigo sobre qualquer perspectiva que apareça. Dez assuntos mais próximos foram buscados com cuidado, permitindo que o conteúdo mantenha o mesmo volume visual do exemplo original.',
      active: true
    },
    {
      title: 'Por que os dezesseis minutos parecem difíceis',
      text: 'Começou de forma cordial e seguiu com detalhes suficientes para preencher a área. A descrição preserva o espaçamento, a leitura e a altura esperada para o item desativado dentro do grupo de lista com descrição.',
      disabled: true
    },
    {
      title: 'Barton esperou vinte para reparar',
      text: 'Conversando na sala enquanto os minutos passavam, o conteúdo se estende com calma para reproduzir o bloco original. Assim o título, o parágrafo e as bordas mantêm proporções iguais às do legado.'
    }
  ];

  return (
    <div className="bootstrapElementsPage__listGroup bootstrapElementsPage__listGroup--last">
      {items.map((item) => (
        <PreventLink key={item.title} className={joinClassNames('bootstrapElementsPage__listGroupItem', item.active ? 'active' : undefined, item.disabled ? 'disabled' : undefined)}>
          <h4 className="bootstrapElementsPage__listGroupHeading">{item.title}</h4>
          <p className="bootstrapElementsPage__listGroupText">{item.text}</p>
        </PreventLink>
      ))}
    </div>
  );
}


export function BootstrapElementsPageTitle(): JSX.Element {
  return (
    <div className="bootstrapElementsPage__pageTitleRow">
      <h4 className="page-title bootstrapElementsPage__pageTitle">Interface Bootstrap</h4>
      <PageBreadcrumb
        items={[
          { label: 'Ubold', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Interface Bootstrap' }
        ]}
      />
    </div>
  );
}

export function OverlayExamplesCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <div className="bootstrapElementsPage__row">
        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Popovers" />
          <Description>Adicione pequenas sobreposições de conteúdo a qualquer elemento para exibir informações secundárias.</Description>
          <div className="button-list bootstrapElementsPage__buttonList">
            <BootstrapOverlayButton type="popover" placement="top" content="Conteúdo de exemplo para este popover.">Popover acima</BootstrapOverlayButton>
            <BootstrapOverlayButton type="popover" placement="bottom" content={'Vivamus\nsagittis lacus vel augue laoreet rutrum faucibus.'}>Popover abaixo</BootstrapOverlayButton>
            <BootstrapOverlayButton type="popover" placement="right" content="Conteúdo de exemplo para este popover.">Popover à direita</BootstrapOverlayButton>
            <BootstrapOverlayButton type="popover" placement="left" title="Título do popover" content="Conteúdo de exemplo para este popover.">Popover à esquerda</BootstrapOverlayButton>
            <BootstrapOverlayButton type="popover" placement="top" dismissible title="Popover dispensável" content="Aqui há um conteúdo de exemplo para o popover. Bem direto, certo?">Popover dispensável</BootstrapOverlayButton>
          </div>
        </div>

        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Tooltips" />
          <Description>Quatro opções estão disponíveis: alinhado acima, à direita, abaixo e à esquerda.</Description>
          <div className="button-list bootstrapElementsPage__buttonList">
            <BootstrapOverlayButton type="tooltip" placement="left" content="Tooltip à esquerda">Tooltip à esquerda</BootstrapOverlayButton>
            <BootstrapOverlayButton type="tooltip" placement="top" content="Tooltip acima">Tooltip acima</BootstrapOverlayButton>
            <BootstrapOverlayButton type="tooltip" placement="bottom" content="Tooltip abaixo">Tooltip abaixo</BootstrapOverlayButton>
            <BootstrapOverlayButton type="tooltip" placement="right" content="Tooltip à direita">Tooltip à direita</BootstrapOverlayButton>
          </div>
        </div>
      </div>
    </CardBox>
  );
}

export function LabelsAndBadgesCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <div className="bootstrapElementsPage__row">
        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Labels" />
          <Description>Adicione qualquer uma das classes modificadoras mencionadas para alterar a aparência de um label.</Description>
          <div className="bootstrapElementsPage__inlineSamples">
            {toneItems.map((item) => <Label key={item.tone} variant={item.tone}>{item.label}</Label>)}
          </div>
        </div>

        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Badge" />
          <Description>Quatro opções estão disponíveis: alinhado acima, à direita, abaixo e à esquerda.</Description>
          <div className="bootstrapElementsPage__inlineSamples bootstrapElementsPage__badgeSamples">
            {badgeItems.map((item) => <Badge key={`${item.tone}-${item.label}`} variant={item.tone}>{item.label}</Badge>)}
          </div>
        </div>
      </div>
    </CardBox>
  );
}

export function AlertsCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <div className="bootstrapElementsPage__row">
        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Alerta padrão" />
          <Description tall>Envolva qualquer texto e um botão opcional de fechamento em <code>.alert</code> e uma das quatro classes contextuais (por exemplo, <code>.alert-success</code>) para mensagens de alerta básicas.</Description>
          <StandardAlerts />
        </div>

        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Alerta fechável" />
          <Description tall>Crie um alerta adicionando um <code>.alert-dismissible</code> opcional e um botão de fechar.</Description>
          <DismissableAlerts />
        </div>
      </div>
    </CardBox>
  );
}

export function PaginationAndPagerCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <div className="bootstrapElementsPage__row">
        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Paginação" />
          <Description>Forneça links de paginação para seu site ou aplicativo com o componente de paginação de várias páginas.</Description>

          <h5 className="bootstrapElementsPage__subTitle"><b>Paginação padrão</b></h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Paginação simples inspirada no Rdio, ótima para aplicativos e resultados de busca.</p>
          <PaginationList />

          <div className="bootstrapElementsPage__spacer bootstrapElementsPage__spacer--sm" />
          <h5 className="bootstrapElementsPage__subTitle"><b>Paginação separada</b></h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Os links ficam separados entre si ao adicionar a classe <code>.pagination-split</code></p>
          <PaginationList split />

          <div className="bootstrapElementsPage__spacer bootstrapElementsPage__spacer--sm" />
          <h5 className="bootstrapElementsPage__subTitle"><b>Tamanhos</b></h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Adicione <code>.pagination-lg</code> ou <code>.pagination-sm</code> para tamanhos adicionais.</p>
          <PaginationList large />
        </div>

        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Paginadores" />
          <Description>Links rápidos de anterior e próximo para implementações simples de paginação com marcação e estilos leves.</Description>

          <h5 className="bootstrapElementsPage__subTitle"><b>Padrão</b></h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Por padrão, o paginador centraliza os links.</p>
          <Pager />

          <div className="bootstrapElementsPage__spacer bootstrapElementsPage__spacer--md" />
          <h5 className="bootstrapElementsPage__subTitle"><b>Links alinhados</b></h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Como alternativa, você pode alinhar cada link nas laterais:</p>
          <Pager aligned />

          <div className="bootstrapElementsPage__spacer bootstrapElementsPage__spacer--md" />
          <h5 className="bootstrapElementsPage__subTitle">Estado desativado opcional</h5>
          <p className="bootstrapElementsPage__mutedText text-muted font-13">Os links do paginador também usam a classe utilitária geral <code>.disabled</code> da paginação.</p>
          <Pager disabled />
        </div>
      </div>
    </CardBox>
  );
}

export function ListGroupsCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <div className="bootstrapElementsPage__row">
        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Grupos de lista (grupo simples)" />
          <Description>O grupo de lista mais básico é uma lista não ordenada com itens de lista e as classes corretas. Expanda com as opções abaixo ou com seu próprio CSS conforme necessário.</Description>
          <SimpleListGroup />
        </div>

        <div className="bootstrapElementsPage__col bootstrapElementsPage__col--half">
          <SectionTitle title="Grupo de lista com links" />
          <Description>Transforme itens do grupo de lista em links usando tags de âncora em vez de itens de lista (pai <code>&lt;div&gt;</code> em vez de <code>&lt;ul&gt;</code>). Não é necessário envolver cada item.</Description>
          <LinkedListGroup />
        </div>
      </div>
    </CardBox>
  );
}

export function DescriptionListGroupCard(): JSX.Element {
  return (
    <CardBox className="bootstrapElementsPage__card">
      <SectionTitle title="Grupo de lista com descrição" />
      <Description>Adicione praticamente qualquer HTML dentro, inclusive em grupos de lista com links como o exemplo abaixo.</Description>
      <DescriptionListGroup />
    </CardBox>
  );
}
