import { useEffect, useRef, useState } from 'react';
import type { PersonPhotoUploadProps } from './PersonPhotoUpload.types';

export const defaultPhotoTypes = ['image/jpeg', 'image/png'] as const;
export const photoTypeLabels = { 'image/jpeg': 'JPG', 'image/png': 'PNG', 'image/webp': 'WEBP' };

export function usePersonPhoto({ file, disabled, onChange, onError, onValidatingChange, acceptedTypes, maxSizeBytes }: Required<Pick<PersonPhotoUploadProps, 'acceptedTypes' | 'maxSizeBytes'>> & Pick<PersonPhotoUploadProps, 'file' | 'disabled' | 'onChange' | 'onError' | 'onValidatingChange'>) {
  const [preview, setPreview] = useState<{ file: File; url: string }>();
  const [validating, setValidating] = useState(false);
  const pending = useRef<{ image: HTMLImageElement; url: string } | null>(null);
  const latest = useRef({ disabled, onChange, onError, onValidatingChange });
  latest.current = { disabled, onChange, onError, onValidatingChange };

  function cancelPending() {
    if (!pending.current) return;
    pending.current.image.onload = null;
    pending.current.image.onerror = null;
    pending.current.image.src = '';
    URL.revokeObjectURL(pending.current.url);
    pending.current = null;
    setValidating(false);
    latest.current.onValidatingChange?.(false);
  }

  useEffect(() => {
    if (!file) { setPreview(undefined); return; }
    const url = URL.createObjectURL(file);
    setPreview({ file, url });
    return () => URL.revokeObjectURL(url);
  }, [file]);
  useEffect(() => cancelPending, []);
  useEffect(() => { if (disabled) cancelPending(); }, [disabled]);

  function selectPhoto(selected: File) {
    if (latest.current.disabled) return;
    cancelPending();
    if (!acceptedTypes.some(type => type === selected.type)) {
      onError(`A foto deve estar nos formatos ${acceptedTypes.map(type => photoTypeLabels[type]).join(', ')}.`);
      return;
    }
    if (selected.size > maxSizeBytes) {
      onError(`A foto não pode ultrapassar ${maxSizeBytes / (1024 * 1024)} MB.`);
      return;
    }
    // Decode locally before handing the file to the form; MIME alone is not enough.
    const image = new Image();
    const url = URL.createObjectURL(selected);
    pending.current = { image, url };
    setValidating(true);
    latest.current.onValidatingChange?.(true);
    image.onload = () => {
      const valid = image.naturalWidth > 0 && image.naturalHeight > 0;
      cancelPending();
      if (latest.current.disabled) return;
      if (valid) latest.current.onChange(selected);
      else latest.current.onError('O arquivo da foto deve ser uma imagem válida.');
    };
    image.onerror = () => {
      cancelPending();
      if (!latest.current.disabled) latest.current.onError('O arquivo da foto deve ser uma imagem válida.');
    };
    image.src = url;
  }

  return { preview: preview && preview.file === file ? preview.url : undefined, validating, selectPhoto, cancelPending };
}
