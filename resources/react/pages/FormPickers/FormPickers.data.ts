import type { DateRangeInputConfig, PickerInputConfig } from './FormPickers.types';

export const timePickerInputs: PickerInputConfig[] = [
  { id: 'timepicker', label: 'Default Time Picker' },
  { id: 'timepicker2', label: '24 Hour Mode Time Picker' },
  { id: 'timepicker3', label: 'Specify a step for the minute field' }
];

export const datePickerInputs: PickerInputConfig[] = [
  { id: 'datepicker', label: 'Default Functionality', placeholder: 'mm/dd/yyyy' },
  { id: 'datepicker-autoclose', label: 'Auto Close', placeholder: 'mm/dd/yyyy' },
  { id: 'datepicker-multiple-date', label: 'Multiple Date', placeholder: 'mm/dd/yyyy' }
];

export const dateRangePickerInputs: DateRangeInputConfig[] = [
  {
    label: 'Date Range Pick',
    className: 'input-daterange-datepicker',
    value: '01/01/2015 - 01/31/2015'
  },
  {
    label: 'Date Range With Time',
    className: 'input-daterange-timepicker',
    value: '01/01/2015 1:30 PM - 01/01/2015 2:00 PM'
  },
  {
    label: 'Limit Selectable Dates',
    className: 'input-limit-datepicker',
    value: '06/01/2015 - 06/07/2015'
  }
];

export const clockPickerInputs: PickerInputConfig[] = [
  { label: 'Default Clock Picker', className: 'clockpicker', value: '09:30' },
  { label: 'Auto close', className: 'clockpicker', value: '13:14' },
  { label: 'Place at left, align the arrow to top ', className: 'clockpicker', value: '13:14' }
];
