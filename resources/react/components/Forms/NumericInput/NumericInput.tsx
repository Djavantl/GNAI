import { useRef } from 'react';
import TextInput from '@/components/Forms/TextInput/TextInput';
import type { NumericInputProps } from './NumericInput.types';
import { useNumericInput } from './useNumericInput';

export default function NumericInput({ format = {}, onValueChange, ...props }: NumericInputProps): React.JSX.Element {
  const ref = useRef<HTMLInputElement>(null);
  useNumericInput(ref, format, onValueChange);
  return <TextInput {...props} ref={ref} />;
}
