export type ErrorCode = '400' | '403' | '404' | '419' | '500' | '503';
export interface ErrorDescription {
  title: string;
  paragraphs: string[];
}
