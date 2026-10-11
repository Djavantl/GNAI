import type { ReactNode } from 'react';

import type { IconGallerySection } from './IconsPage.types';

interface IconGalleryProps {
  pageKey: string;
  sections: IconGallerySection[];
}

function SectionHeading({
  pageKey,
  section,
  sectionIndex
}: {
  pageKey: string;
  section: IconGallerySection;
  sectionIndex: number;
}): React.JSX.Element | null {
  if (!section.title) {
    return null;
  }

  const hasDivider = pageKey === 'icons-weather' || sectionIndex > 0;
  const className = [
    'iconsPage__sectionTitle',
    hasDivider ? 'iconsPage__sectionTitle--divider page-header' : null,
    'header-title',
    sectionIndex === 0 ? 'm-t-0' : null
  ].filter(Boolean).join(' ');

  return (
    <h4 className={className}>
      <b>{section.title}</b>
      {section.badge ? (
        <>
          {' '}
          <span className="iconsPage__label label label-sm label-purple">{section.badge}</span>
        </>
      ) : null}
    </h4>
  );
}

function getSectionAlert(pageKey: string, section: IconGallerySection): ReactNode | null {
  if (pageKey === 'icons-weather' && section.title === 'Ícones das fases da lua') {
    return 'As luas são divididas em 28 ícones para corresponder ao ciclo lunar de 28 dias. Há um conjunto principal e um conjunto alternativo. O conjunto principal deve ser interpretado assim: onde há pixels, essa é a parte iluminada da lua. O conjunto alternativo deve ser interpretado assim: onde há pixels, essa é a parte sombreada da lua.';
  }

  if (pageKey === 'icons-fontawesome' && section.title === 'Ícones de spinner') {
    return (
      <>
        Estes ícones funcionam muito bem com a classe <code>fa-spin</code>. Confira o{' '}
        <a className="iconsPage__sectionAlertLink alert-link" href="#">
          exemplo de ícones girando
        </a>
        .
      </>
    );
  }

  if (pageKey === 'icons-fontawesome' && section.title === 'Ícones de marcas') {
    return (
      <ul className="iconsPage__sectionAlertList margin-bottom-none padding-left-lg">
        <li>Todos os ícones de marcas são marcas comerciais de seus respectivos proprietários.</li>
        <li>
          {' '}
          O uso dessas marcas comerciais não indica endosso do titular da marca pelo Font Awesome, nem vice-versa.
        </li>
      </ul>
    );
  }

  return null;
}

export default function IconGallery({ pageKey, sections }: IconGalleryProps): React.JSX.Element {
  return (
    <>
      {sections.map((section: IconGallerySection, sectionIndex: number) => {
        const sectionAlert = getSectionAlert(pageKey, section);

        return (
          <section className="iconsPage__section" key={`${section.title ?? 'icons'}-${sectionIndex}`}>
            <SectionHeading pageKey={pageKey} section={section} sectionIndex={sectionIndex} />
            {section.descriptionHtml ? (
              <p
                className="iconsPage__description text-muted m-b-15 font-13"
                dangerouslySetInnerHTML={{ __html: section.descriptionHtml }}
              />
            ) : null}

            {sectionAlert ? (
              <div className="iconsPage__sectionAlert alert alert-success">
                {sectionAlert}
              </div>
            ) : null}

            <div className="iconsPage__iconList icon-list-demo">
              {section.icons.map((icon, iconIndex: number) => (
                <div className="iconsPage__iconItem" key={`${sectionIndex}-${iconIndex}-${icon.className}-${icon.label}`}>
                  <i className={icon.className} aria-hidden="true" />
                  <span>{icon.label}</span>
                </div>
              ))}
            </div>
          </section>
        );
      })}
    </>
  );
}
