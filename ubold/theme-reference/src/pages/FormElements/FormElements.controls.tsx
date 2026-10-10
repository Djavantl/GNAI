import { useState, type ReactNode } from 'react';
import Button from '@/Components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import { dropdownMenuItems, multipleSelectOptions } from './FormElements.data';
import type { DropdownMenuItem, FormFieldConfig, FormOption } from './FormElements.types';

export function SectionTitle({ children }: { children: string }): JSX.Element {
  return <h4 className="m-t-0 header-title"><b>{children}</b></h4>;
}

export function Description({ children }: { children: ReactNode }): JSX.Element {
  return <p className="text-muted m-b-30 font-13">{children}</p>;
}

export function FormCard({ children }: { children: ReactNode }): JSX.Element {
  return (
    <div className="formElementsPage__row">
      <div className="formElementsPage__col col-sm-12">
        <div className="card-box formElementsPage__card">{children}</div>
      </div>
    </div>
  );
}

function renderOptions(options: FormOption[] | undefined): JSX.Element[] {
  return (options ?? []).map((option) => (
    <option key={option.value ?? option.label} value={option.value ?? option.label}>
      {option.label}
    </option>
  ));
}

export function FormField({ field }: { field: FormFieldConfig }): JSX.Element {
  const controlType = field.controlType ?? 'input';
  const inputSizeClass = field.controlClassName?.includes('input-sm-wrapper')
    ? 'input-sm'
    : field.controlClassName?.includes('input-lg-wrapper')
      ? 'input-lg'
      : undefined;
  const stateClass = field.state ? `has-${field.state}` : undefined;
  const hasFeedback = field.feedbackIcon ? 'has-feedback' : undefined;

  return (
    <div className={joinClassNames('form-group', field.groupClassName, stateClass, hasFeedback)}>
      {field.label ? (
        <label className={joinClassNames(field.labelClassName, 'control-label')} htmlFor={field.labelFor ?? field.id}>
          {field.label}
        </label>
      ) : null}
      <div className={field.controlClassName}>
        {controlType === 'static' ? (
          <p className="form-control-static">{field.defaultValue}</p>
        ) : null}
        {controlType === 'textarea' ? (
          <textarea className="form-control" rows={field.rows} defaultValue={field.defaultValue} />
        ) : null}
        {controlType === 'select' ? (
          <>
            <select className="form-control">{renderOptions(field.options)}</select>
            {field.afterControl ? <h6>{field.afterControl}</h6> : null}
            {field.afterControl ? (
              <select multiple className="form-control">
                {renderOptions(multipleSelectOptions)}
              </select>
            ) : null}
          </>
        ) : null}
        {controlType === 'input' ? (
          <input
            id={field.id}
            name={field.name}
            type={field.type ?? 'text'}
            className={joinClassNames('form-control', inputSizeClass)}
            placeholder={field.placeholder}
            defaultValue={field.defaultValue}
            readOnly={field.readOnly}
            disabled={field.disabled}
          />
        ) : null}
        {field.feedbackIcon ? <span className={joinClassNames(field.feedbackIcon, 'form-control-feedback')} /> : null}
        {field.helpText ? <span className="help-block"><small>{field.helpText}</small></span> : null}
      </div>
    </div>
  );
}

export function PreventSubmitForm({ className, children }: { className?: string; children: ReactNode }): JSX.Element {
  return (
    <form
      className={className}
      role="form"
      onSubmit={(event) => {
        event.preventDefault();
      }}
    >
      {children}
    </form>
  );
}

export function LegacyCheckbox({ id, label }: { id: string; label: string }): JSX.Element {
  return (
    <div className="checkbox checkbox-primary">
      <input id={id} type="checkbox" />
      <label htmlFor={id}>{label}</label>
    </div>
  );
}

function DropdownMenu({ alignRight = false, items = dropdownMenuItems }: { alignRight?: boolean; items?: DropdownMenuItem[] }): JSX.Element {
  return (
    <ul className={joinClassNames('dropdown-menu', alignRight ? 'dropdown-menu-right' : undefined)}>
      {items.map((item, index) => (
        item.divider ? (
          <li className="divider" key={`divider-${index}`} />
        ) : (
          <li key={item.label ?? index}><a href="#" onClick={(event) => event.preventDefault()}>{item.label}</a></li>
        )
      ))}
    </ul>
  );
}

export function DropdownButton({ label = 'Ação', split = false, alignRight = false }: { label?: string; split?: boolean; alignRight?: boolean }): JSX.Element {
  const [open, setOpen] = useState<boolean>(false);

  if (split) {
    return (
      <div className={joinClassNames('input-group-btn', open ? 'open' : undefined)}>
        <Button tone="primary" type="button" label="Ação" />
        <Button
          tone="primary"
          type="button"
          className="formElementsPage__dropdownToggle"
          aria-label="Abrir menu"
          onClick={() => setOpen((currentValue) => !currentValue)}
        >
          <span className="caret" />
        </Button>
        {open ? <DropdownMenu alignRight={alignRight} /> : null}
      </div>
    );
  }

  const buttonContent = label ? (
    <>
      {label} <span className="caret" />
    </>
  ) : (
    <span className="caret" />
  );

  return (
    <div className={joinClassNames('input-group-btn', open ? 'open' : undefined)}>
      <Button tone="primary" type="button" className="dropdown-toggle" onClick={() => setOpen((currentValue) => !currentValue)}>
        {buttonContent}
      </Button>
      {open ? <DropdownMenu alignRight={alignRight} /> : null}
    </div>
  );
}
