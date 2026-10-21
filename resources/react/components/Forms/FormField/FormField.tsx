import type { ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import InfoTooltip from '@/components/UI/InfoTooltip/InfoTooltip';
import './FormField.css';

export interface FormFieldProps {
  id: string;
  label: string;
  children: ReactNode;
  layout?: 'stacked' | 'horizontal';
  hint?: string;
  hintLabel?: string;
  error?: string;
  className?: string;
  requiredIndicator?: boolean;
}

export default function FormField({ id, label, children, layout = 'stacked', hint, hintLabel, error, className, requiredIndicator = false }: FormFieldProps): React.JSX.Element {
  return (
    <div className={joinClassNames('formField', `formField--${layout}`, className)}>
      {hint && hintLabel ? (
        <div className="formField__labelRow">
          <label className="formField__label" htmlFor={id}>{label}{requiredIndicator && <> <span className="formField__required">*</span></>}</label>
          <InfoTooltip text={hint} label={hintLabel} />
        </div>
      ) : (
        <label className="formField__label" htmlFor={id}>{label}{requiredIndicator && <> <span className="formField__required">*</span></>}</label>
      )}
      <div className="formField__control">
        {children}
        {hint && !hintLabel ? <span id={`${id}-hint`} className="formField__hint">{hint}</span> : null}
        {error ? <label id={`${id}-error`} htmlFor={id} className="formField__error">{error}</label> : null}
      </div>
    </div>
  );
}
