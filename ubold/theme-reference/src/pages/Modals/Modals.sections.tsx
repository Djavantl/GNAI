import Table from '@/Components/Tables/Table/Table';
import { useState, type ReactNode } from 'react';
import Button from '@/Components/UI/Button/Button';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import Panel from '@/Components/UI/Panel/Panel';
import Tabs, { Accordion, type AccordionItem, type TabItem } from '@/Components/Navigation/Tabs/Tabs';
import Modal, { type ModalEffect, type ModalSize, useModalClose } from '@/Components/UI/Modal/Modal';
import './Modals.css';

const modalText = 'Duis mollis, est non commodo luctus, nisi erat porttitor ligula.';
const customboxModalText = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
const overflowText = 'Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.';
const overflowTextShort = 'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.';
const overflowTextLong = 'Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.';
const modalAccordionText = "Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.";
const modalPanelText = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
const modalTabParagraphs = {
  home: [
    'Carriage quitting securing be appetite it declared. High eyes kept so busy feel call in. Would day nor ask walls known. But preserved advantage are but and certainty earnestly enjoyment. Passage weather as up am exposed. And natural related man subject. Eagerness get situation his was delighted.',
    'Fulfilled direction use continual set him propriety continued. Saw met applauded favourite deficient engrossed concealed and her. Concluded boy perpetual old supposing. Farther related bed and passage comfort civilly. Dashwoods see frankness objection abilities the. As hastened oh produced prospect formerly up am. Placing forming nay looking old married few has. Margaret disposed add screened rendered six say his striking confined.'
  ],
  profile: [
    'Fulfilled direction use continual set him propriety continued. Saw met applauded favourite deficient engrossed concealed and her. Concluded boy perpetual old supposing. Farther related bed and passage comfort civilly. Dashwoods see frankness objection abilities the. As hastened oh produced prospect formerly up am. Placing forming nay looking old married few has. Margaret disposed add screened rendered six say his striking confined.',
    'When be draw drew ye. Defective in do recommend suffering. House it seven in spoil tiled court. Sister others marked fat missed did out use. Alteration possession dispatched collecting instrument travelling he or on. Snug give made at spot or late that mr.'
  ],
  messages: [
    'When be draw drew ye. Defective in do recommend suffering. House it seven in spoil tiled court. Sister others marked fat missed did out use. Alteration possession dispatched collecting instrument travelling he or on. Snug give made at spot or late that mr.',
    'Carriage quitting securing be appetite it declared. High eyes kept so busy feel call in. Would day nor ask walls known. But preserved advantage are but and certainty earnestly enjoyment. Passage weather as up am exposed. And natural related man subject. Eagerness get situation his was delighted.'
  ],
  settings: [
    'Luckily friends do ashamed to do suppose. Tried meant mr smile so. Exquisite behaviour as to middleton perfectly. Chicken no wishing waiting am. Say concerns dwelling graceful six humoured. Whether mr up savings talking an. Active mutual nor father mother exeter change six did all.',
    'Carriage quitting securing be appetite it declared. High eyes kept so busy feel call in. Would day nor ask walls known. But preserved advantage are but and certainty earnestly enjoyment. Passage weather as up am exposed. And natural related man subject. Eagerness get situation his was delighted.'
  ]
};
const effects: Array<{ label: string; effect: ModalEffect }> = [
  { label: 'Aparecer', effect: 'fadein' }, { label: 'Deslizar', effect: 'slide' }, { label: 'Jornal', effect: 'newspaper' },
  { label: 'Queda', effect: 'fall' }, { label: 'Queda lateral', effect: 'sidefall' }, { label: 'Desfoque', effect: 'blur' },
  { label: 'Virar', effect: 'flip' }, { label: 'Sinal', effect: 'sign' }, { label: 'Superescala', effect: 'superscaled' },
  { label: 'Fenda', effect: 'slit' }, { label: 'Rotacionar', effect: 'rotate' }, { label: 'Deixar entrar', effect: 'letmein' },
  { label: 'Abrir caminho', effect: 'makeway' }, { label: 'Deslize', effect: 'slip' }, { label: 'Canto', effect: 'corner' },
  { label: 'Deslizar junto', effect: 'slidetogether' }, { label: 'Escala', effect: 'scale' }, { label: 'Porta', effect: 'door' },
  { label: 'Empurrar', effect: 'push' }, { label: 'Escala do conteúdo', effect: 'contentscale' }, { label: 'Expandir', effect: 'swell' },
  { label: 'Rotacionar para baixo', effect: 'rotatedown' }, { label: 'Flash', effect: 'flash' }
];

export function StandardBody(): JSX.Element {
  const paragraphs = [overflowText, overflowTextShort, overflowTextLong, overflowText, overflowTextShort, overflowTextLong, overflowText, overflowTextShort, overflowTextLong];
  return <><h4>Texto em um modal</h4><p>{modalText}</p><hr /><h4>Texto excedente para demonstrar a rolagem</h4>{paragraphs.map((paragraph, index) => <p key={index}>{paragraph}</p>)}</>;
}

export function CustomBody(): JSX.Element { return <><h4>Texto em um modal</h4><p>{modalText}</p><hr /><h4>Texto excedente para demonstrar a rolagem</h4><p>{overflowText}</p></>; }

export function CustomboxAnimationBody(): JSX.Element { return <>{customboxModalText}</>; }

export function TabParagraphs({ paragraphs }: { paragraphs: string[] }): JSX.Element {
  return <>{paragraphs.map((paragraph, index) => <p key={index}>{paragraph}</p>)}</>;
}

export function ResponsiveBody(): JSX.Element {
  return <>
    <div className="modalLayer__formRow"><FormField id="field-1" label="Nome" placeholder="João" width="half" /><FormField id="field-2" label="Sobrenome" placeholder="Silva" width="half" /></div>
    <div className="modalLayer__formRow"><FormField id="field-3" label="Endereço" placeholder="Endereço" /></div>
    <div className="modalLayer__formRow"><FormField id="field-4" label="Cidade" placeholder="Salvador" width="third" /><FormField id="field-5" label="País" placeholder="Brasil" width="third" /><FormField id="field-6" label="CEP" placeholder="40000-000" width="third" /></div>
    <div className="modalLayer__formRow"><FormField id="field-7" label="Informações pessoais" placeholder="Escreva algo sobre você" textarea noMargin /></div>
  </>;
}

export function FormField({ id, label, placeholder, width = 'full', textarea = false, noMargin = false }: { id: string; label: string; placeholder: string; width?: 'full' | 'half' | 'third'; textarea?: boolean; noMargin?: boolean }): JSX.Element {
  return <div className={`modalLayer__formCol modalLayer__formCol--${width}`}>
    <div className={`modalLayer__formGroup ${noMargin ? 'modalLayer__formGroup--noMargin' : ''}`}>
      <label htmlFor={id} className="control-label">{label}</label>
      {textarea ? <textarea id={id} className="modalLayer__formControl form-control autogrow" placeholder={placeholder} /> : <input id={id} type="text" className="modalLayer__formControl form-control" placeholder={placeholder} />}
    </div>
  </div>;
}

export function ModalFooter({ onClose, tone = 'primary' }: { onClose: () => void; tone?: 'primary' | 'info' }): JSX.Element {
  const modalClose = useModalClose();
  return <><Button label="Fechar" tone="default" onClick={modalClose ?? onClose} /><Button label="Salvar alterações" tone={tone} /></>;
}

export function PanelModalBody({ onClose }: { onClose: () => void }): JSX.Element {
  const modalClose = useModalClose();

  return (
    <Panel
      title="Painel primário"
      tone="primary"
      appearance="color"
      className="modalLayer__panel"
      headingAction={<button type="button" className="modalLayer__panelClose close m-t-5" aria-label="Fechar" onClick={modalClose ?? onClose}>×</button>}
      body={<p>{modalPanelText}</p>}
    />
  );
}

export function ModalsShowcase(): JSX.Element {
  const [modal, setModal] = useState<{ size: ModalSize; title?: string; body: ReactNode; footer?: ReactNode; effect?: ModalEffect; bootstrap?: boolean; contentClassName?: string; bodyClassName?: string } | null>(null);
  const close = (): void => setModal(null);
  const open = (size: ModalSize, title: string, body: ReactNode, footer = true): void => setModal({ size, title, body, footer: footer ? <ModalFooter onClose={close} /> : undefined, bootstrap: true });

  const tabItems: TabItem[] = [
    { id: 'modal-home', label: 'Início', icon: 'fa fa-home', content: <TabParagraphs paragraphs={modalTabParagraphs.home} /> },
    { id: 'modal-profile', label: 'Perfil', icon: 'fa fa-user', content: <TabParagraphs paragraphs={modalTabParagraphs.profile} /> },
    { id: 'modal-messages', label: 'Mensagens', icon: 'fa fa-envelope-o', content: <TabParagraphs paragraphs={modalTabParagraphs.messages} /> },
    { id: 'modal-settings', label: 'Configurações', icon: 'fa fa-cog', content: <TabParagraphs paragraphs={modalTabParagraphs.settings} /> }
  ];
  const accordionItems: AccordionItem[] = [1, 2, 3].map((number) => ({ id: `modal-accordion-${number}`, title: `Item do grupo recolhível #${number}`, content: modalAccordionText }));

  return <section className="modalsPage" aria-label="Modais">
    <div className="modalsPage__titleRow"><h4 className="modalsPage__title">Modais</h4><PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'UI Kit', href: '#' }, { label: 'Modais' }]} /></div>

    <CardBox className="modalsPage__card">
      <h4 className="header-title"><b>Modais Bootstrap (padrão)</b></h4><p className="modalsPage__description text-muted">Um modal renderizado com cabeçalho, corpo e ações no rodapé.</p>
      <div className="button-list"><Button label="Modal padrão" tone="primary" onClick={() => open('default', 'Cabeçalho do modal', <StandardBody />)} /><Button label="Modal grande" tone="primary" onClick={() => open('large', 'Modal grande', <p>...</p>, false)} /><Button label="Modal pequeno" tone="primary" onClick={() => open('small', 'Modal pequeno', <p>...</p>, false)} /></div>
    </CardBox>

    <CardBox className="modalsPage__card">
      <h4 className="header-title"><b>Modais personalizados</b></h4><p className="modalsPage__description text-muted">Exemplos de modais personalizados.</p>
      <div className="button-list"><Button label="Modal com largura customizada" tone="primary" onClick={() => open('custom', 'Cabeçalho do modal', <CustomBody />)} /><Button label="Modal em tela cheia" tone="primary" onClick={() => open('full', 'Cabeçalho do modal', <CustomBody />)} /><Button label="Modal responsivo" tone="primary" onClick={() => open('default', 'O conteúdo do modal é responsivo', <ResponsiveBody />, true)} /><Button label="Tabs no modal" tone="primary" onClick={() => setModal({ size: 'default', body: <Tabs items={tabItems} initialActiveId="modal-messages" variant="justified" />, bootstrap: true, contentClassName: 'modalLayer__content--flush', bodyClassName: 'modalLayer__body--tabs' })} /><Button label="Accordion no modal" tone="primary" onClick={() => setModal({ size: 'default', body: <Accordion items={accordionItems} initialActiveId="modal-accordion-2" joined />, bootstrap: true, contentClassName: 'modalLayer__content--flush', bodyClassName: 'modalLayer__body--accordion' })} /><Button label="Painel no modal" tone="primary" onClick={() => setModal({ size: 'default', body: <PanelModalBody onClose={close} />, bootstrap: true, contentClassName: 'modalLayer__content--flush modalLayer__content--panel', bodyClassName: 'modalLayer__body--panel' })} /></div>
    </CardBox>

    <CardBox className="modalsPage__card"><h4 className="header-title"><b>Exemplos de modais (animações)</b></h4><p className="modalsPage__description text-muted">Escolha um efeito para visualizar a animação do modal.</p><Table appearance="inherit" className="modalLayer__animationTable"><thead><tr><th>Tipo de modal</th><th>Exemplo</th></tr></thead><tbody>{effects.map((item) => <tr key={item.effect}><td>{item.label}</td><td><Button label="Mostrar" tone="primary" onClick={() => setModal({ size: 'default', title: 'Título do modal', body: <CustomboxAnimationBody />, effect: item.effect })} /></td></tr>)}</tbody></Table></CardBox>

    <Modal open={modal !== null} title={modal?.title} size={modal?.size} customWidth={modal?.size === 'custom' ? '55%' : undefined} effect={modal?.effect} bootstrap={modal?.bootstrap === true} contentClassName={modal?.contentClassName} bodyClassName={modal?.bodyClassName} onClose={close} footer={modal?.footer}>{modal?.body}</Modal>
  </section>;
}
