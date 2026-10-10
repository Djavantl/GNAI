import type { PanelAppearance, PanelTone } from '@/Components/UI/Panel/Panel';

export interface PanelsPanelItem {
  appearance: PanelAppearance;
  tone: PanelTone;
  title: string;
}

export interface PanelsSectionItem {
  heading?: string;
  panels: PanelsPanelItem[];
}

export const panelsBodyText =
  'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

export const panelsExampleText =
  'Some default panel content here. Nulla vitae elit libero, a pharetra augue. Aenean lacinia bibendum nulla sed consectetur. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Nullam id dolor id nibh ultricies vehicula ut id elit.';

export const borderedPanels: PanelsPanelItem[] = [
  { appearance: 'border', tone: 'default', title: 'Painel padrão' },
  { appearance: 'border', tone: 'custom', title: 'Painel customizado' },
  { appearance: 'border', tone: 'primary', title: 'Painel primário' },
  { appearance: 'border', tone: 'success', title: 'Painel sucesso' },
  { appearance: 'border', tone: 'info', title: 'Painel info' },
  { appearance: 'border', tone: 'warning', title: 'Painel aviso' },
  { appearance: 'border', tone: 'danger', title: 'Painel perigo' },
  { appearance: 'border', tone: 'purple', title: 'Painel roxo' },
  { appearance: 'border', tone: 'pink', title: 'Painel rosa' },
  { appearance: 'border', tone: 'inverse', title: 'Painel inverso' }
];

export const coloredPanels: PanelsPanelItem[] = [
  { appearance: 'standard', tone: 'default', title: 'Painel padrão' },
  { appearance: 'color', tone: 'custom', title: 'Painel customizado' },
  { appearance: 'color', tone: 'primary', title: 'Painel primário' },
  { appearance: 'color', tone: 'success', title: 'Painel sucesso' },
  { appearance: 'color', tone: 'info', title: 'Painel info' },
  { appearance: 'color', tone: 'warning', title: 'Painel aviso' },
  { appearance: 'color', tone: 'danger', title: 'Painel perigo' },
  { appearance: 'color', tone: 'purple', title: 'Painel roxo' },
  { appearance: 'color', tone: 'pink', title: 'Painel rosa' },
  { appearance: 'color', tone: 'inverse', title: 'Painel inverso' }
];

export const footerPanels: PanelsPanelItem[] = [
  { appearance: 'standard', tone: 'default', title: 'Painel padrão' },
  { appearance: 'color', tone: 'custom', title: 'Painel customizado' },
  { appearance: 'color', tone: 'primary', title: 'Painel primário' }
];

export const panelsListItems: string[] = [
  'Cras justo odio',
  'Dapibus ac facilisis in',
  'Morbi leo risus',
  'Porta ac consectetur ac',
  'Vestibulum at eros'
];

export const panelsTableHeaders: string[] = ['#', 'Nome', 'Sobrenome', 'Usuário'];

export const panelsTableRows: string[][] = [
  ['1', 'Mark', 'Otto', '@mdo'],
  ['2', 'Jacob', 'Thornton', '@fat'],
  ['3', 'Larry', 'the Bird', '@twitter']
];
