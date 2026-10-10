import type { ReactNode } from 'react';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { basicFormFields, horizontalFormFields, validationColumns } from './FormValidation.data';
import type { BasicFieldConfig, CheckboxOptionConfig, HorizontalFieldConfig, ValidationFieldConfig } from './FormValidation.types';

function Row({ children }: { children: ReactNode }): JSX.Element {
  return <div className="formValidationPage__row row">{children}</div>;
}

function Col({ children, sm, lg }: { children: ReactNode; sm?: 12; lg?: 6 }): JSX.Element {
  const classes = ['formValidationPage__col'];

  if (sm) {
    classes.push(`col-sm-${sm}`);
  }

  if (lg) {
    classes.push(`col-lg-${lg}`);
  }

  return <div className={classes.join(' ')}>{children}</div>;
}

function SectionTitle({ children, className = 'm-t-0 header-title' }: { children: ReactNode; className?: string }): JSX.Element {
  return <h4 className={className}><b>{children}</b></h4>;
}

function SmallTitle({ children }: { children: ReactNode }): JSX.Element {
  return <h5><b>{children}</b></h5>;
}

function Description({ children, className = 'text-muted font-13 m-b-30' }: { children: ReactNode; className?: string }): JSX.Element {
  return <p className={className}>{children}</p>;
}

type ParsleyAttributeValue = string | number | boolean | undefined;

function parsleyAttributes(field: BasicFieldConfig | ValidationFieldConfig): Record<string, ParsleyAttributeValue> {
  return {
    'data-parsley-trigger': 'parsleyTrigger' in field ? field.parsleyTrigger : undefined,
    'parsley-trigger': 'parsleyTrigger' in field ? field.parsleyTrigger : undefined,
    'data-parsley-equalto': field.parsleyEqualTo,
    'data-parsley-type': field.parsleyType,
    'parsley-type': field.parsleyType,
    'data-parsley-minlength': 'parsleyMinLength' in field ? field.parsleyMinLength : undefined,
    'data-parsley-maxlength': 'parsleyMaxLength' in field ? field.parsleyMaxLength : undefined,
    'data-parsley-length': 'parsleyLength' in field ? field.parsleyLength : undefined,
    'data-parsley-min': 'parsleyMin' in field ? field.parsleyMin : undefined,
    'data-parsley-max': 'parsleyMax' in field ? field.parsleyMax : undefined,
    'data-parsley-pattern': 'parsleyPattern' in field ? field.parsleyPattern : undefined
  };
}

function BasicField({ field }: { field: BasicFieldConfig }): JSX.Element {
  return (
    <div className="form-group">
      <label htmlFor={field.id}>{field.label}</label>
      <input
        id={field.id}
        name={field.name}
        type={field.type}
        placeholder={field.placeholder}
        className="form-control"
        required={field.required}
        {...parsleyAttributes(field)}
      />
    </div>
  );
}

function HorizontalField({ field }: { field: HorizontalFieldConfig }): JSX.Element {
  return (
    <div className="form-group">
      <label htmlFor={field.id} className="col-sm-4 control-label">{field.label}</label>
      <div className="col-sm-7">
        <input
          id={field.id}
          type={field.type}
          placeholder={field.placeholder}
          className="form-control"
          required={field.required}
          {...parsleyAttributes(field)}
        />
      </div>
    </div>
  );
}

function ValidationField({ field }: { field: ValidationFieldConfig }): JSX.Element {
  if (field.id === 'pass2') {
    return (
      <div className="form-group">
        <label className="col-sm-3 control-label">{field.label}</label>
        <div className="col-sm-3">
          <input id="pass2" type="password" className="form-control" required placeholder="Senha" />
        </div>
        <div className="col-sm-3">
          <input type="password" className="form-control" required data-parsley-equalto="#pass2" placeholder="Redigite a senha" />
        </div>
      </div>
    );
  }

  const commonProps = {
    className: 'form-control',
    required: field.required,
    ...parsleyAttributes(field)
  };

  return (
    <div className="form-group">
      <label className="col-sm-3 control-label">{field.label}</label>
      <div className="col-sm-6">
        {field.multiline ? (
          <textarea {...commonProps} />
        ) : (
          <input id={field.id} type={field.type ?? 'text'} placeholder={field.placeholder} {...commonProps} />
        )}
      </div>
    </div>
  );
}

function CheckboxOption({ option }: { option: CheckboxOptionConfig }): JSX.Element {
  return (
    <div className="checkbox checkbox-pink">
      <input
        id={option.id}
        type="checkbox"
        data-parsley-multiple={option.parsleyMultiple}
        data-parsley-mincheck={option.parsleyMinCheck}
        data-parsley-maxcheck={option.parsleyMaxCheck}
        required={option.required}
      />
      <label htmlFor={option.id}>{option.label}</label>
    </div>
  );
}

function ValidationCheckboxGroup({ label, options }: { label: string; options: CheckboxOptionConfig[] }): JSX.Element {
  return (
    <div className="form-group">
      <label className="col-sm-3 control-label">{label}</label>
      <div className="col-sm-6">
        {options.map((option) => <CheckboxOption option={option} key={option.id} />)}
      </div>
    </div>
  );
}

function FormButtons({ alignRight = false, horizontal = false, register = false }: { alignRight?: boolean; horizontal?: boolean; register?: boolean }): JSX.Element {
  const inner = (
    <>
      <button className="btn btn-primary waves-effect waves-light" type="submit">
        {register ? 'Registrar' : 'Enviar'}
      </button>
      <button type="reset" className="btn btn-default waves-effect waves-light m-l-5">
        Cancelar
      </button>
    </>
  );

  if (horizontal) {
    return (
      <div className="form-group">
        <div className="col-sm-offset-4 col-sm-8">{inner}</div>
      </div>
    );
  }

  return <div className={`form-group${alignRight ? ' text-right' : ''} m-b-0`}>{inner}</div>;
}

export function FormValidationPageTitle(): JSX.Element {
  return (
    <div className="formValidationPage__pageTitleRow row">
      <div className="formValidationPage__col col-sm-12">
        <h4 className="page-title">Validação de formulário</h4>
        <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'Formulários', href: '#' }, { label: 'Validação de formulário' }]} />
      </div>
    </div>
  );
}

export function FormValidationExamplesSection(): JSX.Element {
  return (
    <Row>
      <Col lg={6}>
        <CardBox className="formValidationPage__card">
          <SectionTitle>Formulário básico</SectionTitle>
          <Description>Seu texto incrível vem aqui.</Description>
          <form action="#" data-parsley-validate noValidate>
            {basicFormFields.map((field) => <BasicField field={field} key={field.id} />)}
            <div className="form-group">
              <div className="checkbox">
                <input id="remember-1" type="checkbox" />
                <label htmlFor="remember-1"> Lembrar de mim </label>
              </div>
            </div>
            <FormButtons alignRight />
          </form>
        </CardBox>
      </Col>

      <Col lg={6}>
        <CardBox className="formValidationPage__card">
          <SectionTitle>Formulário horizontal</SectionTitle>
          <Description>Seu texto incrível vem aqui.</Description>
          <form className="form-horizontal" role="form" data-parsley-validate noValidate>
            {horizontalFormFields.map((field) => <HorizontalField field={field} key={field.id} />)}
            <div className="form-group">
              <div className="col-sm-offset-4 col-sm-8">
                <div className="checkbox">
                  <input id="remember-2" type="checkbox" />
                  <label htmlFor="remember-2"> Lembrar de mim </label>
                </div>
              </div>
            </div>
            <FormButtons horizontal register />
          </form>
        </CardBox>
      </Col>
    </Row>
  );
}

export function FieldsValidationSection(): JSX.Element {
  return (
    <Row>
      <Col sm={12}>
        <CardBox className="formValidationPage__card">
          <SectionTitle className="m-t-0 m-b-30 header-title">Validação de campos</SectionTitle>
          <Row>
            {validationColumns.map((column) => (
              <Col lg={6} key={column.title}>
                <SmallTitle>{column.title}</SmallTitle>
                <Description>{column.description}</Description>
                <form className="form-horizontal group-border-dashed" action="#">
                  {column.fields.map((field) => <ValidationField field={field} key={`${column.title}-${field.label}`} />)}
                  {column.minCheckOptions ? <ValidationCheckboxGroup label="Seleção mínima" options={column.minCheckOptions} /> : null}
                  {column.maxCheckOptions ? <ValidationCheckboxGroup label="Seleção máxima" options={column.maxCheckOptions} /> : null}
                  <div className="form-group m-b-0">
                    <div className="col-sm-offset-3 col-sm-9 m-t-15">
                      <button type="submit" className="btn btn-primary">Enviar</button>
                      <button type="reset" className="btn btn-default m-l-5">Cancelar</button>
                    </div>
                  </div>
                </form>
              </Col>
            ))}
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}
