import type { BootstrapCarouselSlide } from '@/components/UI/BootstrapCarousel/BootstrapCarousel';
import type { OwlTextCarouselSlide } from '@/components/UI/OwlTextCarousel/OwlTextCarousel.types';

export const bootstrapCaptionSlides: BootstrapCarouselSlide[] = [
  {
    alt: 'Imagem do primeiro slide',
    src: '/assets/react/images/big/img2.jpg',
    caption: {
      title: 'Rótulo do primeiro slide',
      text: 'Texto de exemplo para a legenda do primeiro slide.'
    }
  },
  {
    alt: 'Imagem do segundo slide',
    src: '/assets/react/images/big/img3.jpg',
    caption: {
      title: 'Rótulo do segundo slide',
      text: 'Texto de exemplo para a legenda do segundo slide.'
    }
  },
  {
    alt: 'Imagem do terceiro slide',
    src: '/assets/react/images/big/img4.jpg',
    caption: {
      title: 'Rótulo do terceiro slide',
      text: 'Texto de exemplo para a legenda do terceiro slide.'
    }
  }
];

export const bootstrapImageSlides: BootstrapCarouselSlide[] = [
  { alt: 'Imagem do primeiro slide', src: '/assets/react/images/big/img4.jpg' },
  { alt: 'Imagem do segundo slide', src: '/assets/react/images/big/img3.jpg' },
  { alt: 'Imagem do terceiro slide', src: '/assets/react/images/big/img2.jpg' }
];

export const owlPanelSlides: OwlTextCarouselSlide[] = [
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'custom',
    date: '27 de agosto de 2015',
    text: 'Texto de exemplo para o carrossel, mantendo uma linha longa semelhante ao conteúdo original para preservar a composição visual do card.',
    buttonTone: 'default'
  },
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'primary',
    date: '24 de agosto de 2015',
    text: "Lorem Ipsum é um texto fictício usado pela indústria gráfica e editorial. Ele aparece como conteúdo de exemplo desde os anos 1500, quando um impressor desconhecido organizou tipos para criar um modelo de composição.",
    buttonTone: 'primary'
  },
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'pink',
    date: '21 de agosto de 2015',
    text: "É um fato conhecido que o leitor se distrai com o conteúdo legível de uma página ao observar seu layout. O uso de Lorem Ipsum cria uma distribuição de letras mais natural do que repetir textos como conteúdo aqui.",
    buttonTone: 'pink'
  }
];

export const owlOverlaySlides: OwlTextCarouselSlide[] = [
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'white',
    date: '02 de abril de 2015',
    text: 'Texto de exemplo para o carrossel, mantendo uma linha longa semelhante ao conteúdo original para preservar a composição visual do card.',
    buttonTone: 'warning'
  },
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'white',
    date: '02 de abril de 2015',
    text: "Lorem Ipsum é um texto fictício usado pela indústria gráfica e editorial. Ele aparece como conteúdo de exemplo desde os anos 1500, quando um impressor desconhecido organizou tipos para criar um modelo de composição.",
    buttonTone: 'warning'
  },
  {
    title: 'Olá! Bem-vindo ao Interface',
    titleTone: 'white',
    date: '02 de abril de 2015',
    text: "É um fato conhecido que o leitor se distrai com o conteúdo legível de uma página ao observar seu layout. O uso de Lorem Ipsum cria uma distribuição de letras mais natural do que repetir textos como conteúdo aqui.",
    buttonTone: 'warning'
  }
];

export const owlGalleryImages: string[] = Array.from({ length: 10 }, (_item: unknown, index: number) => `/assets/react/images/gallery/${index + 1}.jpg`);
