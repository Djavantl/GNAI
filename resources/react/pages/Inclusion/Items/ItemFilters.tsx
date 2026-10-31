import { useMemo } from 'react';
import FormField from '@/components/Forms/FormField/FormField';
import FormLayout from '@/components/Forms/FormLayout/FormLayout';
import SelectField from '@/components/Forms/SelectField/SelectField';
import TextInput from '@/components/Forms/TextInput/TextInput';
import {
    TableListClearButton,
    TableListFilterActions,
    TableListMoreFilters,
} from '@/components/Tables/TableList/TableList';
import type { ItemFilters as Filters } from './Items.types';
import './ItemFilters.css';

const booleanOptions = {
    nature: [
        { value: '1', label: 'Digital' },
        { value: '0', label: 'Físico' },
    ],
    status: [
        { value: '1', label: 'Ativo' },
        { value: '0', label: 'Inativo' },
    ],
    availability: [
        { value: '1', label: 'Disponível' },
        { value: '0', label: 'Indisponível' },
    ],
};

interface ItemFiltersProps {
    values: Filters;
    itemTypeOptions: Record<string, string>;
    accessibilityFeatureOptions: Record<string, string>;
    errors: Record<string, string | undefined>;
    onUpdate: (name: keyof Filters, value: string) => void;
    onClear: () => void;
    onSubmit: () => void;
}

export default function ItemFilters({
    values,
    itemTypeOptions,
    accessibilityFeatureOptions,
    errors,
    onUpdate,
    onClear,
    onSubmit,
}: ItemFiltersProps): React.JSX.Element {
    const typeOptions = useMemo(
        () =>
            Object.entries(itemTypeOptions)
                .filter(([value]) => value !== '')
                .map(([value, label]) => ({ value, label })),
        [itemTypeOptions],
    );
    const featureOptions = useMemo(
        () =>
            Object.entries(accessibilityFeatureOptions)
                .filter(([value]) => value !== '')
                .map(([value, label]) => ({ value, label })),
        [accessibilityFeatureOptions],
    );

    return (
        <FormLayout
            className="itemFilterForm"
            aria-label="Filtrar itens"
            onSubmit={(event) => {
                event.preventDefault();
                onSubmit();
            }}
        >
            <div className="itemFilters">
                <FormField
                    id="item-name"
                    label="Descrição"
                    error={errors.name}
                >
                    <TextInput
                        id="item-name"
                        name="name"
                        value={values.name}
                        maxLength={255}
                        placeholder="Filtrar por descrição..."
                        invalid={!!errors.name}
                        aria-describedby={
                            errors.name ? 'item-name-error' : undefined
                        }
                        onChange={(event) =>
                            onUpdate('name', event.target.value)
                        }
                    />
                </FormField>
                <FormField
                    id="item-type"
                    label="Tipo"
                    error={errors.item_type}
                >
                    <SelectField
                        id="item-type"
                        name="item_type"
                        label="Tipo"
                        placeholder="Todos"
                        options={typeOptions}
                        value={values.item_type}
                        aria-invalid={!!errors.item_type}
                        aria-describedby={
                            errors.item_type ? 'item-type-error' : undefined
                        }
                        onChange={(event) =>
                            onUpdate('item_type', event.target.value)
                        }
                    />
                </FormField>
                <FormField
                    id="item-is-digital"
                    label="Natureza"
                    error={errors.is_digital}
                >
                    <SelectField
                        id="item-is-digital"
                        name="is_digital"
                        label="Natureza"
                        placeholder="Todos"
                        options={booleanOptions.nature}
                        value={values.is_digital}
                        aria-invalid={!!errors.is_digital}
                        aria-describedby={
                            errors.is_digital
                                ? 'item-is-digital-error'
                                : undefined
                        }
                        onChange={(event) =>
                            onUpdate('is_digital', event.target.value)
                        }
                    />
                </FormField>
                <FormField
                    id="item-accessibility-feature"
                    label="Recurso de acessibilidade"
                    error={errors.accessibility_feature_id}
                >
                    <SelectField
                        id="item-accessibility-feature"
                        name="accessibility_feature_id"
                        label="Recurso de acessibilidade"
                        placeholder="Todos"
                        options={featureOptions}
                        value={values.accessibility_feature_id}
                        aria-invalid={!!errors.accessibility_feature_id}
                        aria-describedby={
                            errors.accessibility_feature_id
                                ? 'item-accessibility-feature-error'
                                : undefined
                        }
                        onChange={(event) =>
                            onUpdate(
                                'accessibility_feature_id',
                                event.target.value,
                            )
                        }
                    />
                </FormField>
                <TableListFilterActions>
                    <TableListClearButton onClick={onClear} />
                </TableListFilterActions>
            </div>
            <TableListMoreFilters
                active={Boolean(values.is_active || values.available)}
            >
                <div className="itemFilters__additional">
                    <FormField
                        id="item-is-active"
                        label="Status"
                        error={errors.is_active}
                    >
                        <SelectField
                            id="item-is-active"
                            name="is_active"
                            label="Status"
                            placeholder="Todos"
                            options={booleanOptions.status}
                            value={values.is_active}
                            aria-invalid={!!errors.is_active}
                            aria-describedby={
                                errors.is_active
                                    ? 'item-is-active-error'
                                    : undefined
                            }
                            onChange={(event) =>
                                onUpdate('is_active', event.target.value)
                            }
                        />
                    </FormField>
                    <FormField
                        id="item-available"
                        label="Disponibilidade"
                        error={errors.available}
                    >
                        <SelectField
                            id="item-available"
                            name="available"
                            label="Disponibilidade"
                            placeholder="Todos"
                            options={booleanOptions.availability}
                            value={values.available}
                            aria-invalid={!!errors.available}
                            aria-describedby={
                                errors.available
                                    ? 'item-available-error'
                                    : undefined
                            }
                            onChange={(event) =>
                                onUpdate('available', event.target.value)
                            }
                        />
                    </FormField>
                </div>
            </TableListMoreFilters>
        </FormLayout>
    );
}
