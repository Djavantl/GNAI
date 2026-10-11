import Button from '@/components/UI/Button/Button';
import CardBox from '@/components/UI/CardBox/CardBox';
import ImageDisplay from '@/components/UI/ImageDisplay/ImageDisplay';
import MediaObject from '@/components/UI/MediaObject/MediaObject';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  imageShapeExamples,
  imageSizeExamples,
  longMediaText,
  secondMediaParagraph,
  shortMediaText,
  thumbnailExamples,
  type ImageShapeExample,
  type ImageSizeExample,
  type ThumbnailExample
} from './Images.data';
import './Images.css';

function Section({
  children,
  description,
  title
}: {
  children: React.ReactNode;
  description: React.ReactNode;
  title: string;
}): React.JSX.Element {
  return (
    <div className="imagesPage__section">
      <h4 className="imagesPage__sectionTitle header-title m-t-0">
        <b>{title}</b>
      </h4>
      <p className="imagesPage__description text-muted m-b-30 font-13">{description}</p>
      {children}
    </div>
  );
}

export function ShapeAndSizeCard(): React.JSX.Element {
  return (
    <CardBox className="imagesPage__card">
      <div className="imagesPage__row">
        <div className="imagesPage__col imagesPage__col--md6">
          <Section
            title="Formatos de imagens"
            description={
              <>
                Adicione classes a um elemento <code>&lt;img&gt;</code> para estilizar imagens facilmente em qualquer projeto.
              </>
            }
          >
            <div className="imagesPage__row">
              {imageShapeExamples.map((example: ImageShapeExample) => (
                <div key={example.label} className="imagesPage__col imagesPage__col--sm4">
                  <ImageDisplay src={example.src} alt="imagem" shape={example.shape} width={example.width} />
                  <p className="imagesPage__captionLabel imagesPage__captionLabel--shape">
                    <code>{example.label}</code>
                  </p>
                </div>
              ))}
            </div>
          </Section>
        </div>

        <div className="imagesPage__col imagesPage__col--md5 imagesPage__col--mdOffset1">
          <Section
            title="Tamanhos de imagens"
            description={
              <>
                Adicione classes a um elemento <code>&lt;img&gt;</code> para estilizar imagens facilmente em qualquer projeto.
              </>
            }
          >
            <div className="imagesPage__row">
              {imageSizeExamples.map((example: ImageSizeExample) => (
                <div key={example.label} className="imagesPage__col imagesPage__col--sm4">
                  <ImageDisplay src={example.src} alt="imagem" size={example.size} />
                  <p className="imagesPage__captionLabel">
                    <code>{example.label}</code>
                  </p>
                </div>
              ))}
            </div>
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

function ThumbnailCard({ item }: { item: ThumbnailExample }): React.JSX.Element {
  return (
    <div className="imagesPage__thumbnail">
      <ImageDisplay src={item.imageSrc} alt="imagem" />
      <div className="imagesPage__thumbnailCaption">
        <h3 className="imagesPage__thumbnailTitle">{item.title}</h3>
        <p>{item.text}</p>
        <p className="imagesPage__thumbnailActions">
          <Button tone="primary" href="#">Botão</Button>
          <Button tone="default" href="#" className="imagesPage__secondaryButton">Botão</Button>
        </p>
      </div>
    </div>
  );
}

export function ResponsiveAndThumbnailCard(): React.JSX.Element {
  return (
    <CardBox className="imagesPage__card">
      <div className="imagesPage__row">
        <div className="imagesPage__col imagesPage__col--md4">
          <Section
            title="Imagens responsivas"
            description={
              <>
                As imagens no Bootstrap 3 podem ficar responsivas com a adição da classe <code>.img-responsive</code>. Isso aplica <code>max-width: 100%;</code>, <code>height: auto;</code> e <code>display: block;</code> à imagem, fazendo com que ela se ajuste bem ao elemento pai.
              </>
            }
          >
            <div className="imagesPage__row">
              <div className="imagesPage__col imagesPage__col--full">
                <ImageDisplay src="/assets/react/images/big/img1.jpg" alt="imagem" />
              </div>
            </div>
          </Section>
        </div>

        <div className="imagesPage__col imagesPage__col--md7 imagesPage__col--mdOffset1">
          <Section
            title="Conteúdo personalizado da miniatura"
            description="Por padrão, as miniaturas do Bootstrap são projetadas para exibir imagens vinculadas com o mínimo de marcação necessária."
          >
            <div className="imagesPage__row">
              {thumbnailExamples.map((item: ThumbnailExample) => (
                <div key={item.imageSrc} className="imagesPage__col imagesPage__col--md4">
                  <ThumbnailCard item={item} />
                </div>
              ))}
            </div>
          </Section>
        </div>
      </div>
    </CardBox>
  );
}

export function DefaultMediaObjects(): React.JSX.Element {
  return (
    <>
      <MediaObject leftImageSrc="/assets/react/images/users/avatar-1.jpg" heading="Cabeçalho de mídia">
        {longMediaText}
      </MediaObject>
      <MediaObject
        leftImageSrc="/assets/react/images/users/avatar-2.jpg"
        heading="Cabeçalho de mídia"
        nested={
          <MediaObject leftImageSrc="/assets/react/images/users/avatar-3.jpg" heading="Cabeçalho de mídia aninhada">
            {longMediaText}
          </MediaObject>
        }
      >
        {longMediaText}
      </MediaObject>
      <MediaObject rightImageSrc="/assets/react/images/users/avatar-4.jpg" heading="Cabeçalho de mídia">
        {shortMediaText}
      </MediaObject>
      <MediaObject
        leftImageSrc="/assets/react/images/users/avatar-5.jpg"
        rightImageSrc="/assets/react/images/users/avatar-7.jpg"
        heading="Cabeçalho de mídia"
      >
        {shortMediaText}
      </MediaObject>
    </>
  );
}

export function MediaAlignmentObjects(): React.JSX.Element {
  return (
    <>
      <MediaObject leftImageSrc="/assets/react/images/users/avatar-8.jpg" heading="Mídia alinhada ao topo">
        <p>{longMediaText}</p>
        <p>{secondMediaParagraph}</p>
      </MediaObject>
      <MediaObject leftImageSrc="/assets/react/images/users/avatar-9.jpg" leftAlignment="middle" heading="Mídia alinhada ao meio">
        <p>{longMediaText}</p>
        <p>{secondMediaParagraph}</p>
      </MediaObject>
      <MediaObject leftImageSrc="/assets/react/images/users/avatar-10.jpg" leftAlignment="bottom" heading="Mídia alinhada à base">
        <p>{longMediaText}</p>
        <p>{secondMediaParagraph}</p>
      </MediaObject>
    </>
  );
}

export function MediaListObjects(): React.JSX.Element {
  return (
    <ul className="imagesPage__mediaList">
      <MediaObject
        as="li"
        leftImageSrc="/assets/react/images/users/avatar-1.jpg"
        heading="Cabeçalho de mídia"
        nested={
          <>
            <MediaObject
              leftImageSrc="/assets/react/images/users/avatar-2.jpg"
              heading="Cabeçalho de mídia aninhada"
              nested={
                <MediaObject leftImageSrc="/assets/react/images/users/avatar-3.jpg" heading="Cabeçalho de mídia aninhada">
                  {shortMediaText}
                </MediaObject>
              }
            >
              {shortMediaText}
            </MediaObject>
            <MediaObject leftImageSrc="/assets/react/images/users/avatar-4.jpg" heading="Cabeçalho de mídia aninhada">
              {shortMediaText}
            </MediaObject>
          </>
        }
      >
        <p>{shortMediaText}</p>
      </MediaObject>
    </ul>
  );
}

export function MediaCard({
  children,
  description,
  title
}: {
  children: React.ReactNode;
  description: string;
  title: string;
}): React.JSX.Element {
  return (
    <CardBox className="imagesPage__card">
      <h4 className="imagesPage__sectionTitle header-title m-t-0">
        <b>{title}</b>
      </h4>
      <p className="imagesPage__description text-muted m-b-30 font-13">{description}</p>
      <div>{children}</div>
    </CardBox>
  );
}


export function ImagesPageTitle(): React.JSX.Element {
  return (
    <div className="imagesPage__pageTitleRow">
      <h4 className="page-title imagesPage__pageTitle">Imagens</h4>
      <PageBreadcrumb
        items={[
          { label: 'Interface', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Imagens' }
        ]}
      />
    </div>
  );
}
