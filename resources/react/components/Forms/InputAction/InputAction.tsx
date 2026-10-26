import type { ReactNode } from 'react';
import TextInput, { type TextInputProps } from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import './InputAction.css';

export default function InputAction({ label, action, buttonDisabled = false, ...inputProps }: TextInputProps & {
  label?: string;
  action?: ReactNode;
  buttonDisabled?: boolean;
}): React.JSX.Element {
  return <div className="inputAction">
    <TextInput {...inputProps} />
    {action !== undefined ? (
      <span className="inputAction__addon">{action}</span>
    ) : label ? (
      <Button type="submit" tone="pink" width="sm" label={label} disabled={buttonDisabled} />
    ) : null}
  </div>;
}
