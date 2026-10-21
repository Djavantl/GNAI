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
import type { MaterialFilters as Filters } from './AccessibleEducationalMaterials.types';
import './MaterialFilters.css';

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

interface MaterialFiltersProps {
    values: Filters;
    accessibilityFeatureOptions: Record<string, string>;
    errors: Record<string, string | undefined>;
    processing: boolean;
    onUpdate: (name: keyof Filters, value: string) => void;
    onClear: () => void;
    onSubmit: () => void;
}

export default function MaterialFilters({
    values,
    accessibilityFeatureOptions,
    errors,
    processing,
    onUpdate,
    onClear,
    onSubmit,
}: MaterialFiltersProps): React.JSX.Element {
    const featureOptions = useMemo(
        () =>
            Object.entries(accessibilityFeatureOptions)
                .filter(([value]) => value !== '')
                .map(([value, label]) => ({ value, label })),
        [accessibilityFeatureOptions],
    );

    return (
        <FormLayout
            className="materialFilterForm"
            aria-label="Filtrar materiais pedagógicos acessíveis"
            onSubmit={(event) => {
                event.preventDefault();
                onSubmit();
            }}
        >
            <div className="materialFilters">
                <FormField
                    id="material-name"
                    label="Nome"
                    error={errors.name}
                >
                    <TextInput
                        id="material-name"
                        name="name"
                        value={values.name}
                        maxLength={255}
                        placeholder="Filtrar por nome..."
                        disabled={processing}
                        invalid={!!errors.name}
                        aria-describedby={
                            errors.name ? 'material-name-error' : undefined
                        }
                        onChange={(event) =>
                            onUpdate('name', event.target.value)
                        }
                    />
                </FormField>
                <FormField
                    id="material-is-digital"
                    label="Natureza"
                    error={errors.is_digital}
                >
                    <SelectField
                        id="material-is-digital"
                        name="is_digital"
                        label="Natureza"
                        placeholder="Todos"
                        options={booleanOptions.nature}
                        value={values.is_digital}
                        disabled={processing}
                        aria-invalid={!!errors.is_digital}
                        aria-describedby={
                            errors.is_digital
                                ? 'material-is-digital-error'
                                : undefined
                        }
                        onChange={(event) =>
                            onUpdate('is_digital', event.target.value)
                        }
                    />
                </FormField>
                <FormField
                    id="material-accessibility-feature"
                    label="Recurso de acessibilidade"
                    error={errors.accessibility_feature_id}
                >
                    <SelectField
                        id="material-accessibility-feature"
                        name="accessibility_feature_id"
                        label="Recurso de acessibilidade"
                        placeholder="Todos"
                        options={featureOptions}
                        value={values.accessibility_feature_id}
                        disabled={processing}
                        aria-invalid={!!errors.accessibility_feature_id}
                        aria-describedby={
                            errors.accessibility_feature_id
                                ? 'material-accessibility-feature-error'
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
                    <TableListClearButton
                        disabled={processing}
                        onClick={onClear}
                    />
                </TableListFilterActions>
            </div>
            <TableListMoreFilters
                active={Boolean(values.is_active || values.available)}
            >
                <div className="materialFilters__additional">
                    <FormField
                        id="material-is-active"
                        label="Status"
                        error={errors.is_active}
                    >
                        <SelectField
                            id="material-is-active"
                            name="is_active"
                            label="Status"
                            placeholder="Todos"
                            options={booleanOptions.status}
                            value={values.is_active}
                            disabled={processing}
                            aria-invalid={!!errors.is_active}
                            aria-describedby={
                                errors.is_active
                                    ? 'material-is-active-error'
                                    : undefined
                            }
                            onChange={(event) =>
                                onUpdate('is_active', event.target.value)
                            }
                        />
                    </FormField>
                    <FormField
                        id="material-available"
                        label="Disponibilidade"
                        error={errors.available}
                    >
                        <SelectField
                            id="material-available"
                            name="available"
                            label="Disponibilidade"
                            placeholder="Todos"
                            options={booleanOptions.availability}
                            value={values.available}
                            disabled={processing}
                            aria-invalid={!!errors.available}
                            aria-describedby={
                                errors.available
                                    ? 'material-available-error'
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
