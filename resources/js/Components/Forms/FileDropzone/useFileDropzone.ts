import { useEffect, useRef, type RefObject, type MutableRefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { DropzoneConstructor, DropzoneInstance, FileDropzoneProps } from './FileDropzone.types';

export function useFileDropzone(host: RefObject<HTMLDivElement>, props: FileDropzoneProps): MutableRefObject<DropzoneInstance | undefined> {
  const instance = useRef<DropzoneInstance>();
  const callbacks = useRef(props);
  callbacks.current = props;
  const { uploadUrl, maxFileSizeMb = 256, acceptedFiles } = props;
  useEffect(() => {
    let cancelled = false;
    let element: HTMLDivElement | undefined;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/gnai/plugins/dropzone/dropzone.js');
      if (cancelled || !host.current) return;
      const Dropzone = (window as Window & { Dropzone?: DropzoneConstructor }).Dropzone;
      if (!Dropzone) throw new Error('Dropzone indisponível.');
      element = document.createElement('div');
      element.className = 'dropzone';
      element.tabIndex = 0;
      element.setAttribute('role', 'button');
      element.setAttribute('aria-label', 'Selecionar ou arrastar arquivos para envio');
      host.current.append(element);
      const dropzone = new Dropzone(element, {
        url: uploadUrl ?? '#', maxFilesize: maxFileSizeMb, acceptedFiles,
        autoProcessQueue: Boolean(uploadUrl),
        dictDefaultMessage: 'Solte arquivos para enviar',
        dictFallbackMessage: 'Seu navegador não suporta arrastar arquivos.',
        dictFallbackText: 'Selecione os arquivos para enviar.',
        dictFileTooBig: 'Arquivo muito grande ({{filesize}} MiB). Máximo: {{maxFilesize}} MiB.',
        dictInvalidFileType: 'Este tipo de arquivo não é permitido.',
        dictResponseError: 'O servidor respondeu com o código {{statusCode}}.',
        dictCancelUpload: 'Cancelar envio', dictCancelUploadConfirmation: 'Cancelar o envio deste arquivo?',
        dictRemoveFile: 'Remover arquivo', dictMaxFilesExceeded: 'Você não pode adicionar mais arquivos.',
        ...(!uploadUrl ? { accept: (_file: File, done: (error?: string) => void): void => done('Nenhum destino de envio foi configurado.') } : {})
      });
      instance.current = dropzone;
      element.addEventListener('keydown', (event: KeyboardEvent) => {
        if (event.target === element && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); element?.click(); }
      });
      dropzone.on('addedfile', () => callbacks.current.onFilesChange?.([...dropzone.files]));
      dropzone.on('removedfile', () => callbacks.current.onFilesChange?.([...dropzone.files]));
      dropzone.on('success', (file, response) => callbacks.current.onSuccess?.(file as File, response));
      dropzone.on('error', (file, message) => {
        const text = message && typeof message === 'object' && 'error' in message ? String(message.error) : String(message);
        callbacks.current.onError?.(file as File, text);
      });
    }
    void initialize().catch((error: unknown) => console.error('Não foi possível carregar o upload de arquivos.', error));
    return () => { cancelled = true; instance.current?.destroy(); instance.current = undefined; element?.remove(); };
  }, [host, uploadUrl, maxFileSizeMb, acceptedFiles]);
  return instance;
}
