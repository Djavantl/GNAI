import type { ImageDisplayShape, ImageDisplaySize } from '@/components/UI/ImageDisplay/ImageDisplay';

export interface ImageShapeExample {
  label: string;
  shape: ImageDisplayShape;
  src: string;
  width: number;
}

export interface ImageSizeExample {
  label: string;
  size: ImageDisplaySize;
  src: string;
}

export interface ThumbnailExample {
  imageSrc: string;
  text: string;
  title: string;
}

export const imageShapeExamples: ImageShapeExample[] = [
  { label: '.img-rounded', shape: 'rounded', src: '/assets/react/images/small/img1.jpg', width: 200 },
  { label: '.img-circle', shape: 'circle', src: '/assets/react/images/small/img4.jpg', width: 200 },
  { label: '.img-thumbnail', shape: 'thumbnail', src: '/assets/react/images/small/img3.jpg', width: 200 }
];

export const imageSizeExamples: ImageSizeExample[] = [
  { label: '.thumb-lg', size: 'lg', src: '/assets/react/images/small/img1.jpg' },
  { label: '.thumb-md', size: 'md', src: '/assets/react/images/small/img4.jpg' },
  { label: '.thumb-sm', size: 'sm', src: '/assets/react/images/small/img3.jpg' }
];

export const thumbnailText = 'Texto de exemplo da miniatura, com conteúdo curto para demonstrar a legenda. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.';

export const thumbnailExamples: ThumbnailExample[] = [
  { imageSrc: '/assets/react/images/big/img3.jpg', title: 'Título da miniatura', text: thumbnailText },
  { imageSrc: '/assets/react/images/big/img2.jpg', title: 'Título da miniatura', text: thumbnailText },
  { imageSrc: '/assets/react/images/big/img1.jpg', title: 'Título da miniatura', text: thumbnailText }
];

export const longMediaText = 'Este é um texto de exemplo para o objeto de mídia. Ele mostra como o conteúdo acompanha uma imagem à esquerda ou à direita, mantendo alinhamento e espaçamento iguais ao modelo legado.';

export const shortMediaText = 'Este é um texto curto de exemplo para o objeto de mídia. Ele mantém o mesmo espaço visual usado pelo modelo legado.';

export const secondMediaParagraph = 'Este segundo parágrafo demonstra o alinhamento vertical da mídia com mais conteúdo, mantendo o bloco parecido com o exemplo original.';
