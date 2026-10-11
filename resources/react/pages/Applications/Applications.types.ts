export type ApplicationKind =
  "inbox" | "email-read" | "email-compose" | "calendar" | "contact";
export interface ApplicationDefinition {
  key: string;
  kind: ApplicationKind;
  title: string;
}
