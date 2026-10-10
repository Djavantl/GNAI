import type { InputHTMLAttributes } from 'react';
import TextInput from '@/Components/Forms/TextInput/TextInput';
import Button from '@/Components/UI/Button/Button';
import './InputAction.css';

export default function InputAction({ label, ...inputProps }: InputHTMLAttributes<HTMLInputElement> & {
  label: string;
}): JSX.Element {
  return <div className="inputAction">
    <TextInput {...inputProps} />
    <Button type="submit" tone="pink" width="sm" label={label} />
  </div>;
}
