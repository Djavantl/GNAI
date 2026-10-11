import { useMemo } from 'react';
import ImageDisplay from '@/components/UI/ImageDisplay/ImageDisplay';
import OwlCarousel from '@/components/UI/OwlCarousel/OwlCarousel';
import type { OwlCarouselOptions } from '@/components/UI/OwlCarousel/OwlCarousel.types';
import './OwlGalleryCarousel.css';
interface OwlGalleryCarouselProps {
  images: string[];
  interval?: number;
}
export default function OwlGalleryCarousel({
  images,
  interval = 5000,
}: OwlGalleryCarouselProps): React.JSX.Element {
  const options = useMemo<OwlCarouselOptions>(
    () => ({
      loop: true,
      margin: 20,
      nav: false,
      autoplay: true,
      autoplayTimeout: interval,
      responsive: {
        0: { items: 1 },
        480: { items: 2 },
        700: { items: 4 },
        1000: { items: 3 },
        1100: { items: 5 },
      },
    }),
    [interval],
  );
  return (
    <div className="owlGalleryCarousel">
      <OwlCarousel options={options} theme label="Carrossel de imagens">
        {images.map((src, index) => (
          <div className="item" key={`${src}-${index}`}>
            <ImageDisplay
              src={src}
              alt="imagem"
              className="owlGalleryCarousel__image"
            />
          </div>
        ))}
      </OwlCarousel>
    </div>
  );
}
