import { CssAnimationSection, PageTitle } from './ComponentsAnimation.sections';
import './ComponentsAnimation.css';

export default function ComponentsAnimation(): JSX.Element {
  return (
    <section className="componentsAnimationPage">
      <div className="componentsAnimationPage__container">
        <PageTitle />
        <CssAnimationSection />
      </div>
    </section>
  );
}
