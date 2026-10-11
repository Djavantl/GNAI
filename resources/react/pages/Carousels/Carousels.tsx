import { BootstrapCarouselCard, CarouselsPageTitle, OwlGalleryCarouselCard, OwlTextCarouselsCard } from './Carousels.sections';
import './Carousels.css';

export default function Carousels(): React.JSX.Element {
  return (
    <section className="carouselsPage" aria-label="Imagens - Carrossel">
      <CarouselsPageTitle />
      <BootstrapCarouselCard />
      <OwlTextCarouselsCard />
      <OwlGalleryCarouselCard />
    </section>
  );
}
