import { useMemo } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import OwlCarousel from '@/components/UI/OwlCarousel/OwlCarousel';
import type { OwlCarouselOptions } from '@/components/UI/OwlCarousel/OwlCarousel.types';
import OwlTextSlideContent from './OwlTextSlideContent';
import type { OwlTextCarouselSlide } from './OwlTextCarousel.types';
import './OwlTextCarousel.css';
interface OwlTextCarouselProps {
  backgroundImage: string;
  interval?: number;
  loop?: boolean;
  overlay?: boolean;
  showDots?: boolean;
  slides: OwlTextCarouselSlide[];
  transition?: 'fade' | 'slide';
}
export default function OwlTextCarousel({
  backgroundImage,
  interval = 4000,
  loop = true,
  overlay = false,
  showDots = false,
  slides,
  transition = 'slide',
}: OwlTextCarouselProps): React.JSX.Element {
  const options = useMemo<OwlCarouselOptions>(
    () => ({
      loop,
      nav: false,
      autoplay: true,
      autoplayTimeout: interval,
      autoplayHoverPause: true,
      items: 1,
      dots: true,
      animateOut: transition === 'fade' ? 'fadeOut' : false,
    }),
    [interval, loop, showDots, transition],
  );
  return (
    <div
      className={joinClassNames(
        'owlTextCarousel',
        overlay ? 'owlTextCarousel--white' : undefined,
      )}
      style={{ backgroundImage: `url(${backgroundImage})` }}
    >
      {overlay ? <div className="owlTextCarousel__overlay" /> : null}
      <div className="owlTextCarousel__body">
        <OwlCarousel
          options={options}
          theme={showDots}
          label="Carrossel de notícias"
        >
          {slides.map((slide, index) => (
            <div className="item" key={`${slide.date}-${index}`}>
              <OwlTextSlideContent slide={slide} />
            </div>
          ))}
        </OwlCarousel>
      </div>
    </div>
  );
}
