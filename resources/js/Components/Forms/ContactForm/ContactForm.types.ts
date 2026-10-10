export interface ContactFormValues {
  name: string;
  position: string;
  company: string;
  email: string;
}
export interface ContactFormProps {
  onCancel: () => void;
  onSave?: (values: ContactFormValues) => void;
}
