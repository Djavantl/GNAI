import BootstrapCarousel from '@/Components/UI/BootstrapCarousel/BootstrapCarousel';
import CardBox from '@/Components/UI/CardBox/CardBox';
import OwlGalleryCarousel from '@/Components/UI/OwlGalleryCarousel/OwlGalleryCarousel';
import OwlTextCarousel from '@/Components/UI/OwlTextCarousel/OwlTextCarousel';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  bootstrapCaptionSlides,
  bootstrapImageSlides,
  owlGalleryImages,
  owlOverlaySlides,
  owlPanelSlides
} from './Carousels.data';

function SectionTitle({ title }: { title: string }): JSX.Element {
  return (
    <h4 className="carouselsPage__sectionTitle header-title m-t-0">
      <b>{title}</b>
    </h4>
  );
}

function Description({ children }: { children: React.ReactNode }): JSX.Element {
  return <p className="carouselsPage__description text-muted m-b-30 font-13">{children}</p>;
}

export function CarouselsPageTitle(): JSX.Element {
  return (
    <div className="carouselsPage__pageTitleRow">
      <h4 className="page-title carouselsPage__pageTitle">Imagens - Carrossel</h4>
      <PageBreadcrumb
        items={[
          { label: 'Ubold', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Imagens - Carrossel' }
        ]}
      />
    </div>
  );
}

export function BootstrapCarouselCard(): JSX.Element {
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

export function OwlTextCarouselsCard(): JSX.Element {
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
            backgroundImage="/assets/gnai/images/big/img5.jpg"
            slides={owlPanelSlides}
            transition="fade"
          />
        </div>

        <div className="carouselsPage__col carouselsPage__col--half">
          <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
          <OwlTextCarousel
            backgroundImage="/assets/gnai/images/big/img4.jpg"
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

export function OwlGalleryCarouselCard(): JSX.Element {
  return (
    <CardBox className="carouselsPage__card">
      <SectionTitle title="Carrossel Owl" />
      <Description>Um componente de apresentação para alternar entre elementos, como um carrossel.</Description>
      <OwlGalleryCarousel images={owlGalleryImages} />
    </CardBox>
  );
}
