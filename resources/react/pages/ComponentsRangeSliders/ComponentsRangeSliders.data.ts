import type { BootstrapSliderItem, IonRangeSliderItem } from './ComponentsRangeSliders.types';

export const ionRangeSliders: IonRangeSliderItem[] = [
  {
    id: 'range_01',
    title: 'Padrão',
    description: 'Iniciar sem parâmetros'
  },
  {
    id: 'range_02',
    title: 'Mín-Máx',
    description: 'Defina valor mínimo, valor máximo e ponto inicial',
    titleBold: true,
    options: { min: 100, max: 1000, from: 550 }
  },
  {
    id: 'range_03',
    title: 'Prefixo',
    description: 'Mostrando grade e adicionando prefixo "$"',
    titleBold: true,
    options: { type: 'double', grid: true, min: 0, max: 1000, from: 200, to: 800, prefix: '$' }
  },
  {
    id: 'range_04',
    title: 'Intervalo',
    description: 'Configure intervalo com valores negativos',
    titleBold: true,
    options: { type: 'double', grid: true, min: -1000, max: 1000, from: -500, to: 500 }
  },
  {
    id: 'range_05',
    title: 'Passo',
    description: 'Usando passo 250',
    titleBold: true,
    options: { type: 'double', grid: true, min: -1000, max: 1000, from: -500, to: 500, step: 250 }
  },
  {
    id: 'range_06',
    title: 'Valores personalizados',
    description: 'Usando qualquer texto como valor',
    titleBold: true,
    options: {
      grid: true,
      from: 3,
      values: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
    }
  },
  {
    id: 'range_07',
    title: 'Números formatados',
    description: 'Formatação ativa. Melhor!',
    titleBold: true,
    options: { grid: true, min: 1000, max: 1000000, from: 200000, step: 1000, prettify_enabled: true }
  },
  {
    id: 'range_08',
    title: 'Desabilitado',
    description: 'Bloqueie o slider usando a opção disable',
    titleBold: true,
    options: { min: 100, max: 1000, from: 550, disable: true }
  }
];

export const sliderColorItems: BootstrapSliderItem[] = [
  { variant: 'default', min: 0, max: 1000, value: 100, tooltip: 'hide' },
  { variant: 'primary', min: 0, max: 1000, value: 150, tooltip: 'hide' },
  { variant: 'info', min: 0, max: 1000, value: 250, tooltip: 'hide' },
  { variant: 'success', min: 0, max: 1000, value: 350, tooltip: 'hide' },
  { variant: 'warning', min: 0, max: 1000, value: 450, tooltip: 'hide' },
  { variant: 'danger', min: 0, max: 1000, value: 550, tooltip: 'hide' },
  { variant: 'custom', min: 0, max: 1000, value: 650, tooltip: 'hide' },
  { variant: 'purple', min: 0, max: 1000, value: 750, tooltip: 'hide' },
  { variant: 'pink', min: 0, max: 1000, value: 850, tooltip: 'hide' },
  { variant: 'inverse', min: 0, max: 1000, value: 950, tooltip: 'hide' }
];

export const verticalSliderItems: BootstrapSliderItem[] = [
  { variant: 'default', orientation: 'vertical', min: -20, max: 20, value: -13, selection: 'after', tooltip: 'hide' },
  { variant: 'primary', orientation: 'vertical', min: -20, max: 20, value: -11, selection: 'after', tooltip: 'hide' },
  { variant: 'info', orientation: 'vertical', min: -20, max: 20, value: -6, selection: 'after', tooltip: 'hide' },
  { variant: 'success', orientation: 'vertical', min: -20, max: 20, value: -4, selection: 'after', tooltip: 'hide' },
  { variant: 'warning', orientation: 'vertical', min: -20, max: 20, value: -6, selection: 'after', tooltip: 'hide' },
  { variant: 'danger', orientation: 'vertical', min: -20, max: 20, value: -6, selection: 'after', tooltip: 'hide' }
];

export const rangeSliderItems: BootstrapSliderItem[] = [
  { variant: 'default', min: 0, max: 1000, value: [150, 650], step: 1, selection: 'after' },
  { variant: 'primary', min: 0, max: 1000, value: [450, 800], step: 1, selection: 'after' },
  { variant: 'info', min: 0, max: 1000, value: [50, 300], step: 1, selection: 'after' },
  { variant: 'success', min: 0, max: 1000, value: [200, 650], step: 1, selection: 'after' },
  { variant: 'warning', min: 0, max: 1000, value: [0, 300], step: 1, selection: 'after' },
  { variant: 'danger', min: 0, max: 1000, value: [0, 300], step: 1, selection: 'after' }
];
