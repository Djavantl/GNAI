import type { TinyMceStyleFormat } from '@/Components/Forms/TinyMceEditor/TinyMceEditor.types';

export const wysiwigStyleFormats: TinyMceStyleFormat[] = [
  { title: 'Texto em negrito', inline: 'b' },
  { title: 'Texto vermelho', inline: 'span', styles: { color: '#ff0000' } },
  { title: 'Título vermelho', block: 'h1', styles: { color: '#ff0000' } },
  { title: 'Exemplo 1', inline: 'span', classes: 'example1' },
  { title: 'Exemplo 2', inline: 'span', classes: 'example2' },
  { title: 'Estilos de tabela' },
  { title: 'Linha de tabela 1', selector: 'tr', classes: 'tablerow1' }
];
