import Button from '@/Components/UI/Button/Button';
import type { OwlTextCarouselSlide } from './OwlTextCarousel.types';

export default function OwlTextSlideContent({ slide }: { slide: OwlTextCarouselSlide }): JSX.Element {
  return (
    <>
      <h3 className="owlTextCarousel__title">
        <a
          href="#"
          className={`owlTextCarousel__titleLink owlTextCarousel__titleLink--${slide.titleTone}`}
          onClick={(event: React.MouseEvent<HTMLAnchorElement>) => event.preventDefault()}
        >
          {slide.title}
        </a>
      </h3>
      <p className="owlTextCarousel__date">{slide.date}</p>
      <p className="owlTextCarousel__text">
        <em>{slide.text}</em>
      </p>
      <Button tone={slide.buttonTone} size="sm" waves={false} className="owlTextCarousel__button">
        Leia mais
      </Button>
    </>
  );
}
