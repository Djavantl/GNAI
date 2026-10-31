import { useState } from 'react';
import FormField from '@/components/Forms/FormField/FormField';
import SelectField from '@/components/Forms/SelectField/SelectField';
import Button from '@/components/UI/Button/Button';
import ReportColumns from './ReportColumns';
import type { ReportRelation } from './Reports.types';

export default function ReportRelations({ relations, added, selected, canAdd, onAdd, onRemove, onToggle }: {
    relations: ReportRelation[];
    added: ReportRelation[];
    selected: string[];
    canAdd: boolean;
    onAdd: (name: string) => void;
    onRemove: (name: string) => void;
    onToggle: (key: string, checked: boolean) => void;
}): React.JSX.Element {
    const [relation, setRelation] = useState('');
    const remaining = relations.filter(item => !added.some(value => value.name === item.name));
    return (
        <div className="reportBuilder__relations">
            <h3>Informações complementares <small>(opcional)</small></h3>
            <p className="reportBuilder__hint">
                Inclua informações associadas ao assunto escolhido, como responsáveis, documentos ou registros relacionados.
            </p>
            {canAdd ? (
                <div className="reportBuilder__source">
                    <FormField id="report-relation" label="Adicionar informação complementar">
                        <SelectField
                            id="report-relation"
                            name="relation"
                            label="Adicionar informação complementar"
                            value={relation}
                            options={remaining.map(item => ({ value: item.name, label: item.label }))}
                            onChange={event => setRelation(event.target.value)}
                        />
                    </FormField>
                    <Button
                        label="Adicionar"
                        tone="save"
                        leftIconClass="fa fa-plus"
                        disabled={!remaining.some(item => item.name === relation)}
                        onClick={() => {
                            onAdd(relation);
                            setRelation('');
                        }}
                    />
                </div>
            ) : null}
            {added.map(item => (
                <section key={item.name} className="reportBuilder__relation" aria-label={item.label}>
                    <div className="reportBuilder__relationHeader">
                        <h3>{item.label}</h3>
                        <Button
                            label="Remover"
                            tone="cancel"
                            title={`Remover informações de ${item.label}`}
                            onClick={() => onRemove(item.name)}
                        />
                    </div>
                    <ReportColumns
                        id={`report-relation-${item.name}`}
                        label={item.label}
                        options={Object.fromEntries(Object.entries(item.columns).map(([key, label]) => [`${item.name}.${key}`, label]))}
                        selected={selected}
                        errors={{}}
                        onChange={onToggle}
                    />
                    {item.pivot && Object.keys(item.pivot.columns).length ? (
                        <>
                            <p className="reportBuilder__hint">Informações adicionais do vínculo</p>
                            <ReportColumns
                                id={`report-pivot-${item.name}`}
                                label={`${item.label}: informações do vínculo`}
                                options={Object.fromEntries(Object.entries(item.pivot.columns).map(([key, label]) => [`${item.name}.pivot.${key}`, label]))}
                                selected={selected}
                                errors={{}}
                                onChange={onToggle}
                            />
                        </>
                    ) : null}
                </section>
            ))}
        </div>
    );
}
