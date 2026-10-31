import FormField from '@/components/Forms/FormField/FormField';
import SelectField from '@/components/Forms/SelectField/SelectField';
import TextInput from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import { operatorLabels, selectOptions } from './Reports.utils';
import type { FilterDefinition, Operator, ReportFilter } from './Reports.types';

export default function ReportFilters({ filters, definitions, errors, onChange, onRemove }: {
    filters: ReportFilter[];
    definitions: FilterDefinition[];
    errors: Record<string, string>;
    onChange: (id: number, update: Partial<ReportFilter>) => void;
    onRemove: (id: number) => void;
}): React.JSX.Element {
    return (
        <div className="reportBuilder__filters">
            {filters.map((filter, index) => {
                const definition = definitions.find(item => item.key === filter.field);
                const options = selectOptions(definition?.options ?? {});
                return (
                    <div key={filter.id} className="reportBuilder__filter">
                        {(['field', 'operator', 'value'] as const).map(part => {
                            const id = `report-filter-${filter.id}-${part}`;
                            const error = errors[`filters.${index}.${part}`];
                            const label = part === 'field' ? 'Informação' : part === 'operator' ? 'Como procurar' : 'O que procurar';
                            const props = { id, name: id, 'aria-invalid': Boolean(error), 'aria-describedby': error ? `${id}-error` : undefined };
                            return (
                                <FormField key={part} id={id} label={label} error={error}>
                                    {part === 'field' ? (
                                        <SelectField
                                            {...props}
                                            label={label}
                                            value={filter.field}
                                            options={definitions.map(item => ({ value: item.key, label: item.label }))}
                                            onChange={event => onChange(filter.id, { field: event.target.value })}
                                        />
                                    ) : part === 'operator' ? (
                                        <SelectField
                                            {...props}
                                            label={label}
                                            value={filter.operator}
                                            options={(definition?.operators ?? []).map(value => ({ value, label: operatorLabels[value] }))}
                                            onChange={event => onChange(filter.id, { operator: event.target.value as Operator })}
                                        />
                                    ) : options.length ? (
                                        <SelectField
                                            {...props}
                                            label={label}
                                            value={filter.value}
                                            options={options}
                                            onChange={event => onChange(filter.id, { value: event.target.value })}
                                        />
                                    ) : (
                                        <TextInput
                                            {...props}
                                            invalid={Boolean(error)}
                                            type={definition?.type === 'date' ? 'date' : 'text'}
                                            value={filter.value}
                                            placeholder="Digite o que deseja encontrar..."
                                            onChange={event => onChange(filter.id, { value: event.target.value })}
                                        />
                                    )}
                                </FormField>
                            );
                        })}
                        <Button
                            tone="cancel"
                            label="Remover"
                            title={`Remover critério de busca ${index + 1}`}
                            onClick={() => onRemove(filter.id)}
                        />
                    </div>
                );
            })}
            {errors.filters ? <p className="formField__error">{errors.filters}</p> : null}
        </div>
    );
}
