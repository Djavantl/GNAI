import { forwardRef, type TextareaHTMLAttributes } from "react";
import "@/Components/Forms/TextInput/TextInput.css";
import "./TextArea.css";
const TextArea = forwardRef<
  HTMLTextAreaElement,
  TextareaHTMLAttributes<HTMLTextAreaElement>
>(function TextArea({ className = "", ...props }, ref): JSX.Element {
  return (
    <textarea
      {...props}
      ref={ref}
      className={`textInput textArea ${className}`}
    />
  );
});
export default TextArea;
