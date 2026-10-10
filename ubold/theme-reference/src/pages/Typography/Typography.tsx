import {
  AddressesAndQuotesSection,
  DescriptionListsSection,
  DropcapAndCodeSection,
  HeadingsSection,
  InlineTextSection,
  ListsSection,
  TypographyPageTitle
} from './Typography.sections';
import './Typography.css';

export default function Typography(): JSX.Element {
  return (
    <section className="typographyPage" aria-label="Tipografia">
      <TypographyPageTitle />
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--full">
          <HeadingsSection />
          <InlineTextSection />
          <AddressesAndQuotesSection />
          <ListsSection />
          <DescriptionListsSection />
          <DropcapAndCodeSection />
        </div>
      </div>
    </section>
  );
}
