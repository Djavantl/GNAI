import { forwardRef, type InputHTMLAttributes } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import './TextInput.css';

export interface TextInputProps extends InputHTMLAttributes<HTMLInputElement> {
  invalid?: boolean;
}

const TextInput = forwardRef<HTMLInputElement, TextInputProps>(function TextInput(
  { className, invalid = false, type = 'text', ...props }, ref
): React.JSX.Element {
  return <input {...props} ref={ref} type={type} className={joinClassNames('textInput', className)} aria-invalid={invalid || undefined} />;
});

export default TextInput;
