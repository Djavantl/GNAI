import TimePicker from '@/components/Forms/TimePicker/TimePicker';
import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { clockPickerInputs, datePickerInputs, dateRangePickerInputs, timePickerInputs } from './FormPickers.data';
import type { DateRangeInputConfig, PickerInputConfig } from './FormPickers.types';

function Row({ children }: { children: ReactNode }): React.JSX.Element {
  return <div className="formPickersPage__row row">{children}</div>;
}

function Col({ children, sm, lg }: { children: ReactNode; sm?: 8 | 12; lg?: 4 | 6 | 8 | 9 | 10 }): React.JSX.Element {
  const classes = ['formPickersPage__col'];

  if (sm) {
    classes.push(`col-sm-${sm}`);
  }

  if (lg) {
    classes.push(`col-lg-${lg}`);
  }

  return <div className={classes.join(' ')}>{children}</div>;
}

function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return <h4 className="m-t-0 header-title"><b>{children}</b></h4>;
}

function Description({ children, className = 'text-muted font-13' }: { children: ReactNode; className?: string }): React.JSX.Element {
  return <p className={className}>{children}</p>;
}

function preventSubmit(event: React.FormEvent<HTMLFormElement>): void {
  event.preventDefault();
}

function TimePickerInput({ input, isLast = false }: { input: PickerInputConfig; isLast?: boolean }): React.JSX.Element {
  return (
    <>
      <label htmlFor={input.id}>{input.label}</label>
      <div className={isLast ? 'm-b-0' : 'm-b-15'}><TimePicker id={input.id} label={input.label} showMeridian={input.id !== 'timepicker2'} /></div>
    </>
  );
}

function DatePickerInput({ input }: { input: PickerInputConfig }): React.JSX.Element {
  return (
    <div className="form-group">
      <label className="control-label col-sm-4" htmlFor={input.id}>{input.label}</label>
      <div className="col-sm-8">
        <div className="input-group">
          <input type="text" className="form-control" placeholder={input.placeholder} id={input.id} />
          <span className="input-group-addon bg-custom b-0 text-white"><i className="icon-calender" /></span>
        </div>
      </div>
    </div>
  );
}

function DateRangePickerInput({ input, isLast = false }: { input: DateRangeInputConfig; isLast?: boolean }): React.JSX.Element {
  return (
    <div className={`form-group${isLast ? ' m-b-0' : ''}`}>
      <label className="col-lg-4 control-label">{input.label}</label>
      <div className="col-lg-8">
        <input className={`form-control ${input.className}`} type="text" name="daterange" defaultValue={input.value} />
      </div>
    </div>
  );
}

function ClockPickerInput({ input, isLast = false, placementTop = false }: { input: PickerInputConfig; isLast?: boolean; placementTop?: boolean }): React.JSX.Element {
  return (
    <>
      <label>{input.label}</label>
      <div className={`input-group ${input.className ?? ''}${isLast ? '' : ' m-b-15'}`} data-placement={placementTop ? 'top' : undefined} data-align={placementTop ? 'top' : undefined} data-autoclose={input.label === 'Auto close' ? 'true' : undefined}>
        <input type="text" className="form-control" defaultValue={input.value} />
        <span className="input-group-addon"> <span className="glyphicon glyphicon-time" /> </span>
      </div>
    </>
  );
}

export function FormPickersPageTitle(): React.JSX.Element {
  return (
    <div className="formPickersPage__pageTitleRow row">
      <div className="formPickersPage__col col-sm-12">
        <h4 className="page-title">Form Pickers</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Forms', href: '#' }, { label: 'Form Pickers' }]} />
      </div>
    </div>
  );
}

export function TimeAndColorPickersSection(): React.JSX.Element {
  return (
    <Row>
      <Col sm={12}>
        <CardBox className="formPickersPage__card">
          <Row>
            <Col lg={6}>
              <SectionTitle>Timepicker</SectionTitle>
              <Description>Easily select a time for a text input using your mouse or keyboards arrow keys.</Description>

              <div className="p-20">
                {timePickerInputs.map((input: PickerInputConfig, index: number) => (
                  <TimePickerInput input={input} isLast={index === timePickerInputs.length - 1} key={input.id} />
                ))}
              </div>
            </Col>

            <Col lg={6}>
              <SectionTitle>Colorpicker</SectionTitle>
              <Description>Add color picker to field or to any other element.</Description>

              <div className="p-20">
                <form action="#" onSubmit={preventSubmit}>
                  <div className="form-group">
                    <label htmlFor="colorpicker-default">Default</label>
                    <input id="colorpicker-default" type="text" className="colorpicker-default form-control" defaultValue="#8fff00" />
                  </div>
                  <div className="form-group">
                    <label htmlFor="colorpicker-rgba">RGBA</label>
                    <input id="colorpicker-rgba" type="text" className="colorpicker-rgba form-control" defaultValue="rgb(0,194,255,0.78)" data-color-format="rgba" />
                  </div>
                  <div className="form-group m-b-0">
                    <label htmlFor="colorpicker-component">As Component</label>
                    <div data-color-format="rgb" data-color="rgb(255, 146, 180)" className="colorpicker-default input-group">
                      <input id="colorpicker-component" type="text" readOnly defaultValue="" className="form-control" />
                      <span className="input-group-btn add-on">
                        <button className="btn btn-white" type="button">
                          <i style={{ backgroundColor: 'rgb(124, 66, 84)', marginTop: 2 }} />
                        </button>
                      </span>
                    </div>
                  </div>
                </form>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function DatePickerSection(): React.JSX.Element {
  return (
    <Row>
      <Col sm={12}>
        <CardBox className="formPickersPage__card">
          <Row>
            <Col lg={10}>
              <SectionTitle>Date Picker</SectionTitle>
              <Description className="text-muted font-13 m-b-30">
                The datepicker is tied to a standard form input field. Click on the input to open
                {' '}an interactive calendar in a small overlay. Click elsewhere on the page or hit the Esc
                {' '}key to close. If a date is chosen, feedback is shown as the input&apos;s value.
              </Description>
            </Col>
          </Row>

          <Row>
            <Col lg={8}>
              <div className="p-20">
                <form action="#" className="form-horizontal" onSubmit={preventSubmit}>
                  {datePickerInputs.map((input: PickerInputConfig) => <DatePickerInput input={input} key={input.id} />)}

                  <div className="form-group">
                    <label className="control-label col-sm-4">Date Range</label>
                    <div className="col-sm-8">
                      <div className="input-daterange input-group" id="date-range">
                        <input type="text" className="form-control" name="start" />
                        <span className="input-group-addon bg-custom b-0 text-white">to</span>
                        <input type="text" className="form-control" name="end" />
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </Col>

            <Col lg={4}>
              <div className="p-20">
                <label>Display Inline</label>
                <div className="input-group">
                  <div id="datepicker-inline" />
                </div>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function DateRangePickerSection(): React.JSX.Element {
  return (
    <Row>
      <Col sm={12}>
        <CardBox className="formPickersPage__card">
          <SectionTitle>Date Range Picker</SectionTitle>

          <Row>
            <Col lg={9}>
              <div className="p-20">
                <form className="form-horizontal" onSubmit={preventSubmit}>
                  <div className="form-group">
                    <label className="col-lg-4 control-label">With all options</label>
                    <div className="col-lg-8">
                      <div id="reportrange" className="pull-right form-control">
                        <i className="glyphicon glyphicon-calendar fa fa-calendar" />
                        <span />
                      </div>
                    </div>
                  </div>

                  {dateRangePickerInputs.map((input: DateRangeInputConfig, index: number) => (
                    <DateRangePickerInput input={input} isLast={index === dateRangePickerInputs.length - 1} key={input.label} />
                  ))}
                </form>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function ClockPickerSection(): React.JSX.Element {
  return (
    <Row>
      <Col sm={12}>
        <CardBox className="formPickersPage__card">
          <SectionTitle>Clock Picker</SectionTitle>

          <Row>
            <Col lg={6}>
              <div className="p-20">
                <ClockPickerInput input={clockPickerInputs[0]} />
                <ClockPickerInput input={clockPickerInputs[1]} isLast placementTop />
              </div>
            </Col>

            <Col lg={6}>
              <div className="p-20">
                <label htmlFor="single-input">Now time</label>
                <div className="input-group m-b-15">
                  <input className="form-control" id="single-input" defaultValue="" placeholder="Now" />
                  <span className="input-group-btn">
                    <button type="button" id="check-minutes" className="btn waves-effect waves-light btn-primary">Check the minutes</button>
                  </span>
                </div>

                <ClockPickerInput input={clockPickerInputs[2]} isLast placementTop />
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}
