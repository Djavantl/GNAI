import { GridOptionsTable, GridStructureExample, PageTitle } from './ComponentsGrid.sections';
import './ComponentsGrid.css';

export default function ComponentsGrid(): React.JSX.Element {
  return (
    <section className="componentsGridPage">
      <div className="componentsGridPage__container">
        <PageTitle />
        <div className="componentsGridPage__row">
          <div className="componentsGridPage__col componentsGridPage__col--full">
            <GridOptionsTable />
          </div>
        </div>
        <div className="componentsGridPage__row">
          <div className="componentsGridPage__col componentsGridPage__col--full">
            <GridStructureExample />
          </div>
        </div>
      </div>
    </section>
  );
}
