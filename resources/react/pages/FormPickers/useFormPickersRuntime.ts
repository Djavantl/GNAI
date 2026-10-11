import { useEffect, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { loadExternalStylesheet } from '@/utils/loadExternalStylesheet';

interface JQueryCollection {
  clockpicker?: (options?: Record<string, unknown> | string, value?: string) => JQueryCollection;
  colorpicker?: (options?: Record<string, unknown>) => JQueryCollection;
  datepicker?: (options?: Record<string, unknown>) => JQueryCollection;
  daterangepicker?: (options?: Record<string, unknown>, callback?: (start: MomentLike, end: MomentLike, label: string) => void) => JQueryCollection;
  on?: (eventName: string, handler: (event: Event) => void) => JQueryCollection;
  timepicker?: (options?: Record<string, unknown>) => JQueryCollection;
}

interface JQueryRuntime {
  (element: Element | string): JQueryCollection;
  fn?: {
    clockpicker?: unknown;
    colorpicker?: unknown;
    datepicker?: unknown;
    daterangepicker?: unknown;
    timepicker?: unknown;
  };
}

interface MomentLike {
  endOf(unit: string): MomentLike;
  format(format: string): string;
  startOf(unit: string): MomentLike;
  subtract(amount: number, unit: string): MomentLike;
  toISOString(): string;
}

interface MomentRuntime {
  (): MomentLike;
}

interface FormPickersWindow extends Window {
  jQuery?: JQueryRuntime;
  moment?: MomentRuntime;
  $?: JQueryRuntime;
}

const formPickersStylesheets: string[] = [
  '/assets/react/plugins/timepicker/bootstrap-timepicker.min.css',
  '/assets/react/plugins/mjolnic-bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
  '/assets/react/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
  '/assets/react/plugins/clockpicker/jquery-clockpicker.min.css',
  '/assets/react/plugins/bootstrap-daterangepicker/daterangepicker.css'
];

const formPickersScripts: string[] = [
  '/assets/react/js/jquery.min.js',
  '/assets/react/js/bootstrap.min.js',
  '/assets/react/plugins/moment/moment.js',
  '/assets/react/plugins/timepicker/bootstrap-timepicker.min.js',
  '/assets/react/plugins/mjolnic-bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
  '/assets/react/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
  '/assets/react/plugins/clockpicker/jquery-clockpicker.min.js',
  '/assets/react/plugins/bootstrap-daterangepicker/daterangepicker.js'
];

function getJQueryRuntime(): JQueryRuntime | null {
  const runtimeWindow = window as FormPickersWindow;
  return runtimeWindow.jQuery ?? runtimeWindow.$ ?? null;
}

function getMomentRuntime(): MomentRuntime | null {
  return (window as FormPickersWindow).moment ?? null;
}

async function loadFormPickersAssets(): Promise<void> {
  await Promise.all(formPickersStylesheets.map((href: string) => loadExternalStylesheet(href)));

  // The legacy page loads plugin defaults before the theme overrides.
  const themeStylesheet = Array.from(document.head.querySelectorAll('style, link[rel="stylesheet"]'))
    .find((element: Element) => !formPickersStylesheets.includes(element.getAttribute('href') ?? ''));
  for (const href of formPickersStylesheets) {
    const stylesheet = document.head.querySelector(`link[href="${href}"]`);
    if (stylesheet && themeStylesheet) {
      document.head.insertBefore(stylesheet, themeStylesheet);
    }
  }

  for (const src of formPickersScripts) {
    await loadExternalScript(src);
  }
}

function initializeColorPickers(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.colorpicker) {
    return;
  }

  root.querySelectorAll('.colorpicker-default').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formPickersReady === 'colorpicker') {
      return;
    }

    runtime(element).colorpicker?.({ format: 'hex' });
    (element as HTMLElement).dataset.formPickersReady = 'colorpicker';
  });

  root.querySelectorAll('.colorpicker-rgba').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formPickersReady === 'colorpicker') {
      return;
    }

    runtime(element).colorpicker?.();
    (element as HTMLElement).dataset.formPickersReady = 'colorpicker';
  });
}

function initializeDatePickers(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.datepicker) {
    return;
  }

  const basicDatePickerConfigs = [
    ['#datepicker', {}],
    ['#datepicker-autoclose', { autoclose: true, todayHighlight: true }],
    ['#datepicker-inline', {}],
    ['#datepicker-multiple-date', { format: 'mm/dd/yyyy', clearBtn: true, multidate: true, multidateSeparator: ',' }],
    ['#date-range', { toggleActive: true }]
  ] as const;

  basicDatePickerConfigs.forEach(([selector, options]) => {
    const element = root.querySelector(selector);

    if (!element || (element as HTMLElement).dataset.formPickersReady === 'datepicker') {
      return;
    }

    runtime(element).datepicker?.(options);
    (element as HTMLElement).dataset.formPickersReady = 'datepicker';
  });
}

function initializeClockPickers(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.clockpicker) {
    return;
  }

  root.querySelectorAll('.clockpicker').forEach((element: Element) => {
    if ((element as HTMLElement).dataset.formPickersReady === 'clockpicker') {
      return;
    }

    runtime(element).clockpicker?.({ donetext: 'Done' });
    (element as HTMLElement).dataset.formPickersReady = 'clockpicker';
  });

  const singleInput = root.querySelector('#single-input');

  if (singleInput && (singleInput as HTMLElement).dataset.formPickersReady !== 'clockpicker') {
    runtime(singleInput).clockpicker?.({
      placement: 'bottom',
      align: 'left',
      autoclose: true,
      default: 'now'
    });
    (singleInput as HTMLElement).dataset.formPickersReady = 'clockpicker';
  }

  const checkMinutesButton = root.querySelector('#check-minutes');

  if (checkMinutesButton && (checkMinutesButton as HTMLElement).dataset.formPickersReady !== 'clockpicker-trigger') {
    checkMinutesButton.addEventListener('click', (event: Event) => {
      event.stopPropagation();
      runtime('#single-input').clockpicker?.('show').clockpicker?.('toggleView', 'minutes');
    });
    (checkMinutesButton as HTMLElement).dataset.formPickersReady = 'clockpicker-trigger';
  }
}

function initializeDateRangePickers(root: HTMLElement, runtime: JQueryRuntime): void {
  if (!runtime.fn?.daterangepicker) {
    return;
  }

  const moment = getMomentRuntime();

  if (!moment) {
    return;
  }

  const rangeInputs = [
    ['.input-daterange-datepicker', { buttonClasses: ['btn', 'btn-sm'], applyClass: 'btn-default', cancelClass: 'btn-white' }],
    [
      '.input-daterange-timepicker',
      {
        timePicker: true,
        format: 'MM/DD/YYYY h:mm A',
        timePickerIncrement: 30,
        timePicker12Hour: true,
        timePickerSeconds: false,
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-default',
        cancelClass: 'btn-white'
      }
    ],
    [
      '.input-limit-datepicker',
      {
        format: 'MM/DD/YYYY',
        minDate: '06/01/2015',
        maxDate: '06/30/2015',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-default',
        cancelClass: 'btn-white',
        dateLimit: { days: 6 }
      }
    ]
  ] as const;

  rangeInputs.forEach(([selector, options]) => {
    const element = root.querySelector(selector);

    if (!element || (element as HTMLElement).dataset.formPickersReady === 'daterangepicker') {
      return;
    }

    runtime(element).daterangepicker?.(options);
    (element as HTMLElement).dataset.formPickersReady = 'daterangepicker';
  });

  const reportRange = root.querySelector('#reportrange');
  const reportRangeLabel = reportRange?.querySelector('span');

  if (!reportRange || !reportRangeLabel || (reportRange as HTMLElement).dataset.formPickersReady === 'daterangepicker') {
    return;
  }

  reportRangeLabel.innerHTML = `${moment().subtract(29, 'days').format('MMMM D, YYYY')} - ${moment().format('MMMM D, YYYY')}`;

  runtime(reportRange).daterangepicker?.(
    {
      format: 'MM/DD/YYYY',
      startDate: moment().subtract(29, 'days'),
      endDate: moment(),
      minDate: '01/01/2012',
      maxDate: '12/31/2015',
      dateLimit: { days: 60 },
      showDropdowns: true,
      showWeekNumbers: true,
      timePicker: false,
      timePickerIncrement: 1,
      timePicker12Hour: true,
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      opens: 'left',
      drops: 'down',
      buttonClasses: ['btn', 'btn-sm'],
      applyClass: 'btn-default',
      cancelClass: 'btn-white',
      separator: ' to ',
      locale: {
        applyLabel: 'Submit',
        cancelLabel: 'Cancel',
        fromLabel: 'From',
        toLabel: 'To',
        customRangeLabel: 'Custom',
        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        firstDay: 1
      }
    },
    (start: MomentLike, end: MomentLike, label: string): void => {
      void label;
      reportRangeLabel.innerHTML = `${start.format('MMMM D, YYYY')} - ${end.format('MMMM D, YYYY')}`;
    }
  );

  (reportRange as HTMLElement).dataset.formPickersReady = 'daterangepicker';
}

export function useFormPickersRuntime(rootRef: RefObject<HTMLElement | null>): void {
  useEffect(() => {
    let cancelled = false;

    async function initializePickers(): Promise<void> {
      await loadFormPickersAssets();

      if (cancelled || !rootRef.current) {
        return;
      }

      const runtime = getJQueryRuntime();

      if (!runtime) {
        return;
      }

      initializeColorPickers(rootRef.current, runtime);
      initializeDatePickers(rootRef.current, runtime);
      initializeClockPickers(rootRef.current, runtime);
      initializeDateRangePickers(rootRef.current, runtime);
    }

    void initializePickers();

    return () => {
      cancelled = true;
    };
  }, [rootRef]);
}
