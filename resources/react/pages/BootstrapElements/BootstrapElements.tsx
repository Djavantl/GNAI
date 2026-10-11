import {
  AlertsCard,
  BootstrapElementsPageTitle,
  DescriptionListGroupCard,
  LabelsAndBadgesCard,
  ListGroupsCard,
  OverlayExamplesCard,
  PaginationAndPagerCard
} from './BootstrapElements.sections';
import './BootstrapElements.css';

export default function BootstrapElements(): React.JSX.Element {
  return (
    <section className="bootstrapElementsPage" aria-label="Interface Bootstrap">
      <BootstrapElementsPageTitle />
      <OverlayExamplesCard />
      <LabelsAndBadgesCard />
      <AlertsCard />
      <PaginationAndPagerCard />
      <ListGroupsCard />
      <DescriptionListGroupCard />
    </section>
  );
}
