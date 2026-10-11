import './ChoiceControl.css';

export type ChoiceType = 'checkbox' | 'radio';
export type ChoiceTone = 'default' | 'custom' | 'primary' | 'success' | 'info' | 'warning' | 'danger' | 'purple' | 'pink' | 'inverse';

interface ChoiceControlProps {
  type: ChoiceType;
  id: string;
  label?: React.ReactNode;
  name?: string;
  tone?: ChoiceTone;
  checked?: boolean;
  disabled?: boolean;
  circle?: boolean;
  inline?: boolean;
  single?: boolean;
  className?: string;
  inputClassName?: string;
  labelClassName?: string;
  'aria-label'?: string;
  onChange?: (checked: boolean) => void;
}

export default function ChoiceControl({ type, id, label, name, tone = 'default', checked = false, disabled = false, circle = false, inline = false, single = false, className: customClassName, inputClassName, labelClassName, 'aria-label': accessibleLabel, onChange }: ChoiceControlProps): React.JSX.Element {
  const className = `${type} ${type}-${tone}${circle ? ` ${type}-circle` : ''}${inline ? ` ${type}-inline` : ''}${single ? ` ${type}-single` : ''}${customClassName ? ` ${customClassName}` : ''}`;

  return (
    <div className={className}>
      <input id={id} name={name} type={type} defaultChecked={onChange ? undefined : checked} checked={onChange ? checked : undefined} onChange={onChange ? (event) => onChange(event.target.checked) : undefined} className={inputClassName} disabled={disabled} aria-label={accessibleLabel ?? (typeof label === 'string' ? label || id : label === undefined ? id : undefined)} />
      <label htmlFor={id} className={labelClassName}>{label}</label>
    </div>
  );
}
