import TextInput, { type TextInputProps } from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import './InputAction.css';

export default function InputAction({ label, buttonDisabled = false, ...inputProps }: TextInputProps & {
  label: string;
  buttonDisabled?: boolean;
}): React.JSX.Element {
  return <div className="inputAction">
    <TextInput {...inputProps} />
    <Button type="submit" tone="pink" width="sm" label={label} disabled={buttonDisabled} />
  </div>;
}
