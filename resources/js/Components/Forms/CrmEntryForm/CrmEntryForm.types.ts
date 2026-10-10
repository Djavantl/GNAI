export type CrmEntryKind = "person" | "opportunity" | "lead";
export interface CrmEntryField {
  name: string;
  label: string;
  placeholder: string;
  type?: "email";
}
