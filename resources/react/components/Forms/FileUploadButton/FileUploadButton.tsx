import type { ChangeEventHandler } from "react";
import "@/components/UI/Button/Button.css";
import "./FileUploadButton.css";
export default function FileUploadButton({
  label,
  accept,
  onChange,
}: {
  label: string;
  accept?: string;
  onChange?: ChangeEventHandler<HTMLInputElement>;
}): React.JSX.Element {
  return (
    <div className="fileUploadButton button button--purple button--md button--width-md waves-effect waves-light">
      <span>
        <i className="ion-upload" aria-hidden="true" />
        {label}
      </span>
      <input
        type="file"
        aria-label={label}
        accept={accept}
        onChange={onChange}
      />
    </div>
  );
}
