import type { InputHTMLAttributes } from 'react';
import TextInput from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import './InputAction.css';

export default function InputAction({ label, ...inputProps }: InputHTMLAttributes<HTMLInputElement> & {
  label: string;
}): React.JSX.Element {
  return <div className="inputAction">
    <TextInput {...inputProps} />
    <Button type="submit" tone="pink" width="sm" label={label} />
  </div>;
}
