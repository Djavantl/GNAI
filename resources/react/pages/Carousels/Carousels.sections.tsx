import BootstrapCarousel from '@/components/UI/BootstrapCarousel/BootstrapCarousel';
import CardBox from '@/components/UI/CardBox/CardBox';
import OwlGalleryCarousel from '@/components/UI/OwlGalleryCarousel/OwlGalleryCarousel';
import OwlTextCarousel from '@/components/UI/OwlTextCarousel/OwlTextCarousel';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  bootstrapCaptionSlides,
  bootstrapImageSlides,
  owlGalleryImages,
  owlOverlaySlides,
  owlPanelSlides
} from './Carousels.data';

function SectionTitle({ title }: { title: string }): React.JSX.Element {
  return (
    <h4 className="carouselsPage__sectionTitle header-title m-t-0">
      <b>{title}</b>
    </h4>
  );
}

function Description({ children }: { children: React.ReactNode }): React.JSX.Element {
  return <p className="carouselsPage__description text-muted m-b-30 font-13">{children}</p>;
}

export function CarouselsPageTitle(): React.JSX.Element {
  return (
    <div className="carouselsPage__pageTitleRow">
      <h4 className="page-title carouselsPage__pageTitle">Imagens - Carrossel</h4>
      <PageBreadcrumb
        items={[
          { label: 'Interface', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Imagens - Carrossel' }
        ]}
      />
    </div>
  );
}

export function BootstrapCarouselCard(): React.JSX.Element {
  return (
    <CardBox className="carouselsPage__card">
      <div className="carouselsPage__row">
        <div className="carouselsPage__col carouselsPage__col--full">
          <SectionTitle title="Carrossel Bootstrap" />
        </div>
      </div>

      <div className="carouselsPage__row">
        <div className="carouselsPage__col carouselsPage__col--half">
          <Description>
            Adicione legendas aos slides facilmente com o elemento <code>.carousel-caption</code> dentro de qualquer <code>.item</code>.
          </Description>
          <BootstrapCarousel ariaLabel="Carrossel Bootstrap com legendas" slides={bootstrapCaptionSlides} controls />
        </div>

        <div className="carouselsPage__col carouselsPage__col--half">
          <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
          <BootstrapCarousel ariaLabel="Carrossel Bootstrap" slides={bootstrapImageSlides} />
        </div>
      </div>
    </CardBox>
  );
}

export function OwlTextCarouselsCard(): React.JSX.Element {
  return (
    <CardBox className="carouselsPage__card">
      <div className="carouselsPage__row">
        <div className="carouselsPage__col carouselsPage__col--full">
          <SectionTitle title="Carrossel Owl" />
        </div>
      </div>

      <div className="carouselsPage__row">
        <div className="carouselsPage__col carouselsPage__col--half">
          <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
          <OwlTextCarousel
            backgroundImage="/assets/react/images/big/img5.jpg"
            slides={owlPanelSlides}
            transition="fade"
          />
        </div>

        <div className="carouselsPage__col carouselsPage__col--half">
          <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
          <OwlTextCarousel
            backgroundImage="/assets/react/images/big/img4.jpg"
            slides={owlOverlaySlides}
            transition="slide"
            loop={false}
            overlay
          />
        </div>
      </div>
    </CardBox>
  );
}

export function OwlGalleryCarouselCard(): React.JSX.Element {
  return (
    <CardBox className="carouselsPage__card">
      <SectionTitle title="Carrossel Owl" />
      <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
      <OwlGalleryCarousel images={owlGalleryImages} />
    </CardBox>
  );
}
