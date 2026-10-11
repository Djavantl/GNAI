export type ErrorCode = '400' | '403' | '404' | '500' | '503';
export interface ErrorDescription { title: string; paragraphs: string[]; supportLink?: boolean; }
