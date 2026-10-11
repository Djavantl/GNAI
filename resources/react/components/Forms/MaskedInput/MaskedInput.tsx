import { useRef } from 'react';
import TextInput, { type TextInputProps } from '@/components/Forms/TextInput/TextInput';
import { useInputMask } from './useInputMask';

export interface MaskedInputProps extends Omit<TextInputProps, 'type' | 'value' | 'onChange'> {
  mask: string;
  onValueChange?: (value: string, rawValue: string) => void;
}

export default function MaskedInput({ mask, onValueChange, ...props }: MaskedInputProps): React.JSX.Element {
  const ref = useRef<HTMLInputElement>(null);
  useInputMask(ref, mask, onValueChange);
  return <TextInput {...props} ref={ref} data-mask={mask} />;
}
