import type { CSSProperties } from 'react';

export type PersonPhotoType = 'image/jpeg' | 'image/png' | 'image/webp';

export interface PersonPhotoUploadProps {
  id?: string;
  name?: string;
  label?: string;
  current?: string | null;
  file: File | null;
  removed?: boolean;
  disabled?: boolean;
  error?: string;
  size?: CSSProperties['width'];
  acceptedTypes?: readonly PersonPhotoType[];
  maxSizeBytes?: number;
  onChange: (file: File) => void;
  onRemove: () => void;
  onError: (message: string) => void;
  onValidatingChange?: (validating: boolean) => void;
}
