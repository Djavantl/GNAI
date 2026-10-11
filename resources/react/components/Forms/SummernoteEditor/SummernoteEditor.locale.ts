import type { SummernoteRuntime } from './SummernoteEditor.types';

/** Complete translations missing from the locale bundled with the legacy editor. */
export function completeSummernoteLocale(runtime: SummernoteRuntime): void {
  const locale = runtime.summernote.lang['pt-BR'];
  Object.assign(locale.font, { subscript: 'Subscrito', superscript: 'Sobrescrito' });
  Object.assign(locale.image, {
    resizeFull: 'Tamanho completo', resizeHalf: 'Metade do tamanho', resizeQuarter: 'Um quarto do tamanho',
    floatLeft: 'Alinhar à esquerda', floatRight: 'Alinhar à direita', floatNone: 'Remover alinhamento',
    shapeRounded: 'Formato: cantos arredondados', shapeCircle: 'Formato: círculo', shapeThumbnail: 'Formato: miniatura', shapeNone: 'Formato: original',
    dragImageHere: 'Arraste uma imagem ou texto para cá', dropImage: 'Solte a imagem ou o texto',
    maximumFileSize: 'Tamanho máximo do arquivo', maximumFileSizeError: 'O arquivo excede o tamanho máximo.',
    url: 'URL da imagem', remove: 'Remover imagem'
  });
  Object.assign(locale.paragraph, { right: 'Alinhar à direita' });
}
