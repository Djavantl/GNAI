import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import CardBox from '@/Components/UI/CardBox/CardBox';
import Table, { TableContents, tableAttributes } from '@/Components/Tables/Table/Table';
import SearchableTable from '@/Components/Tables/SearchableTable/SearchableTable';
import BootstrapTable from '@/Components/Tables/BootstrapTable/BootstrapTable';
import FooTable, { type FooTableMode } from '@/Components/Tables/FooTable/FooTable';
import ResponsiveTable from '@/Components/Tables/ResponsiveTable/ResponsiveTable';
import EditableTable from '@/Components/Tables/EditableTable/EditableTable';
import InlineEditableTable from '@/Components/Tables/InlineEditableTable/InlineEditableTable';
import { tableExamples } from './TablesPage.data';
import {
  tablePageTitles,
  basicSections,
  bootstrapSections,
  fooSections,
} from './TablesPage.sections.data';
import './TablesPage.css';
export default function TablesPage({
  variant,
}: {
  variant: string;
}): JSX.Element {
  const title = tablePageTitles[variant];
  const definitions = tableExamples[variant];
  const basic = (index: number): JSX.Element => (
    <div key={index}>
      <h4>{basicSections[index].title}</h4>
      <p className="tablesPage__description tablesPage__description--short">
        {basicSections[index].description
          .split('`')
          .map((part, i) => (i % 2 ? <code key={i}>{part}</code> : part))}
      </p>
      <div className="tablesPage__basicTable tableSurface table-responsive">
        <Table {...tableAttributes(definitions[index].attributes)}>
          <TableContents definition={definitions[index]} />
        </Table>
      </div>
    </div>
  );
  return (
    <section className="tablesPage" aria-label={title}>
      <h4 className="tablesPage__title">{title}</h4>
      <PageBreadcrumb
        items={[
          { label: 'Ubold', href: '#' },
          { label: 'Tabelas', href: '#' },
          { label: title },
        ]}
      />
      {variant === 'basic' &&
        [[0, 1], [2, 3], [4], [5, 6]].map((group, index) => (
          <CardBox key={index}>
            <div
              className={`tablesPage__grid ${group.length === 1 ? 'tablesPage__grid--single' : ''}`}
            >
              {group.map(basic)}
            </div>
          </CardBox>
        ))}
      {variant === 'datatable' && (
        <CardBox className="tablesPage__scroll">
          <SearchableTable definition={definitions[0]} />
        </CardBox>
      )}
      {variant === 'bootstrap' &&
        definitions.map((definition, index) => (
          <CardBox key={index}>
            <h4>{bootstrapSections[index][0]}</h4>
            <p
              className={`tablesPage__description ${index === 1 || index === 2 ? '' : 'tablesPage__description--short'}`}
            >
              {bootstrapSections[index][1]}
            </p>
            <BootstrapTable definition={definition} removable={index === 5} />
          </CardBox>
        ))}
      {variant === 'foo-tables' &&
        definitions.map((definition, index) => (
          <CardBox key={index}>
            <h4>{fooSections[index][0]}</h4>
            <p className="tablesPage__description">{fooSections[index][1]}</p>
            <FooTable
              definition={definition}
              mode={
                (
                  [
                    'filter',
                    'rows',
                    'toggle',
                    'pagination',
                    'accordion',
                  ] as FooTableMode[]
                )[index]
              }
            />
          </CardBox>
        ))}
      {variant === 'responsive' && (
        <CardBox>
          <ResponsiveTable definition={definitions[0]} />
        </CardBox>
      )}
      {variant === 'editable' && (
        <>
          <div className="tablesPage__editablePanel">
            <EditableTable definition={definitions[0]} />
          </div>
          <CardBox>
            <h4>Editor de células</h4>
            <p className="tablesPage__description">
              Comece a digitar para editar ou navegue usando as setas do teclado
              e cliques do mouse!
            </p>
            <InlineEditableTable definition={definitions[1]} />
          </CardBox>
        </>
      )}
    </section>
  );
}
