import type { TextInputProps } from '@/Components/Forms/TextInput/TextInput';

export interface NumericFormat {
  groupSeparator?: string;
  decimalSeparator?: string;
  currencySymbol?: string;
  symbolPosition?: 'prefix' | 'suffix';
  groupSize?: 2 | 3 | 4;
  min?: number;
  max?: number;
  negativeBrackets?: boolean;
}
export interface NumericInputProps extends Omit<TextInputProps, 'type' | 'value' | 'onChange' | 'min' | 'max'> {
  format?: NumericFormat;
  onValueChange?: (value: string, rawValue: string) => void;
}
