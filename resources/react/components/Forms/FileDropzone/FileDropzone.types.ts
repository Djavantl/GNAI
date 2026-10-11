export interface FileDropzoneProps {
  /** Endpoint accepting multipart/form-data with the field `file`. */
  uploadUrl?: string;
  maxFileSizeMb?: number;
  acceptedFiles?: string;
  onFilesChange?: (files: File[]) => void;
  onSuccess?: (file: File, response: unknown) => void;
  onError?: (file: File, message: string) => void;
}
export interface FileDropzoneHandle { submit: () => void; }
export interface DropzoneInstance {
  files: File[];
  on: (event: string, callback: (...args: unknown[]) => void) => void;
  processQueue: () => void;
  destroy: () => void;
}
export interface DropzoneConstructor { new(element: HTMLElement, options: Record<string, unknown>): DropzoneInstance; }
