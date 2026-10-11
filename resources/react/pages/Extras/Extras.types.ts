export type ExtrasKind =
  | "profile"
  | "timeline"
  | "sitemap"
  | "invoice"
  | "email-template"
  | "maintenance"
  | "coming-soon"
  | "faq"
  | "search-result"
  | "gallery"
  | "pricing";
export interface ExtrasDefinition {
  key: string;
  kind: ExtrasKind;
  title: string;
  standalone: boolean;
}
