import type { InputHTMLAttributes } from "react";
import "@/components/UI/Button/Button.css";
import "./FileUploadButton.css";
export default function FileUploadButton({
  label,
  accept,
  onChange,
  ...props
}: {
  label: string;
  accept?: string;
} & Omit<InputHTMLAttributes<HTMLInputElement>, 'type' | 'value'>): React.JSX.Element {
  return (
    <div className="fileUploadButton button button--purple button--md button--width-md waves-effect waves-light">
      <span>
        <i className="ion-upload" aria-hidden="true" />
        {label}
      </span>
      <input
        {...props}
        type="file"
        aria-label={label}
        accept={accept}
        onChange={onChange}
      />
    </div>
  );
}
