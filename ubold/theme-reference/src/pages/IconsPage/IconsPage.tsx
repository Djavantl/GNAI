import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import FontAwesomeExamples from './FontAwesomeExamples';
import IconGallery from './IconGallery';
import WeatherUtilityClasses from './WeatherUtilityClasses';
import { iconsPages } from './IconsPage.data';
import type { IconsPageData } from './IconsPage.types';
import './IconsPage.css';

interface IconsPageProps {
  pageKey: string;
}

export default function IconsPage({ pageKey }: IconsPageProps): JSX.Element {
  const page = iconsPages.find((item: IconsPageData) => item.key === pageKey) ?? iconsPages[0];

  return (
    <section className={`iconsPage iconsPage--${page.key}`}>
      <div className="iconsPage__container">
        <div className="iconsPage__pageTitleRow">
          <div className="iconsPage__col iconsPage__col--full">
            <h4 className="page-title iconsPage__pageTitle">{page.title}</h4>
            <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'Ícones', href: '#' }, { label: page.breadcrumb }]} />
          </div>
        </div>

        <div className="iconsPage__row">
          <div className="iconsPage__col iconsPage__col--full">
            <CardBox className="iconsPage__card">
              <IconGallery pageKey={page.key} sections={page.sections} />
              {page.key === 'icons-weather' ? <WeatherUtilityClasses /> : null}
            </CardBox>

            {page.key === 'icons-fontawesome' ? (
              <CardBox className="iconsPage__card iconsPage__card--examples">
                <FontAwesomeExamples />
              </CardBox>
            ) : null}
          </div>
        </div>
      </div>
    </section>
  );
}
