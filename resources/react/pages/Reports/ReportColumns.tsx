import ChoiceControl from '@/components/Forms/ChoiceControl/ChoiceControl';
import ChoiceGroup from '@/components/Forms/ChoiceGroup/ChoiceGroup';
import type { ReportOptions } from './Reports.types';

export default function ReportColumns({ id, label, options, selected, errors, onChange }: {
    id: string;
    label: string;
    options: ReportOptions;
    selected: string[];
    errors: Record<string, string>;
    onChange: (key: string, checked: boolean) => void;
}): React.JSX.Element {
    const error = errors.columns || Object.entries(errors).find(([key]) => key.startsWith('columns.'))?.[1];
    return (
        <div className="reportBuilder__columnGroup">
            <ChoiceGroup id={id} label={label} invalid={Boolean(error)} describedBy={error ? `${id}-error` : undefined}>
                {Object.entries(options).map(([key, text], index) => (
                    <ChoiceControl
                        key={key}
                        type="checkbox"
                        id={`${id}-${index}`}
                        label={text}
                        tone="primary"
                        checked={selected.includes(key)}
                        onChange={checked => onChange(key, checked)}
                    />
                ))}
            </ChoiceGroup>
            {error ? <p id={`${id}-error`} className="formField__error">{error}</p> : null}
        </div>
    );
}
