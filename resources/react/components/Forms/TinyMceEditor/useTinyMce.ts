import { useEffect, useRef, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { legacyTinyMcePlugins, legacyTinyMceToolbar } from './TinyMceEditor.config';
import type { TinyMceEditorProps, TinyMceInstance, TinyMceRuntime } from './TinyMceEditor.types';

export function useTinyMce(host: RefObject<HTMLDivElement | null>, { defaultValue = '', height = 300, label = 'Editor de texto', onChange, toolbar = legacyTinyMceToolbar, styleFormats, disabled = false, invalid = false, describedBy, onLoadError }: TinyMceEditorProps): void {
  const initialValue = useRef(defaultValue);
  const callback = useRef(onChange);
  callback.current = onChange;
  const editorRef = useRef<TinyMceInstance | undefined>(undefined);
  const accessibility = useRef({ disabled, invalid, describedBy });
  accessibility.current = { disabled, invalid, describedBy };
  const failure = useRef(onLoadError);
  failure.current = onLoadError;
  function updateAccessibility(editor: TinyMceInstance): void {
    const state = accessibility.current;
    const body = editor.getBody();
    body.setAttribute('aria-label', label);
    body.setAttribute('aria-invalid', String(state.invalid));
    const description = state.describedBy ? document.getElementById(state.describedBy)?.textContent : null;
    if (description) body.setAttribute('aria-description', description);
    else body.removeAttribute('aria-description');
    editor.setMode(state.disabled ? 'readonly' : 'design');
  }
  useEffect(() => {
    if (editorRef.current?.initialized) updateAccessibility(editorRef.current);
  }, [disabled, invalid, describedBy]);
  useEffect(() => {
    let cancelled = false;
    let instance: TinyMceInstance | undefined;
    let textarea: HTMLTextAreaElement | undefined;
    const root = host.current;
    const auxiliaryWindows = new Set<HTMLElement>();
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript('/assets/react/plugins/tinymce/tinymce.min.js');
      await loadExternalScript('/assets/react/plugins/tinymce/langs/pt_BR.js');
      if (cancelled || !root) return;
      const runtime = (window as Window & { tinymce?: TinyMceRuntime }).tinymce;
      if (!runtime) throw new Error('TinyMCE indisponível.');
      textarea = document.createElement('textarea');
      textarea.id = `${root.id}-input`;
      textarea.setAttribute('aria-label', label);
      textarea.value = initialValue.current;
      root.append(textarea);
      runtime.init({
        selector: `#${textarea.id}`, theme: 'modern', height,
        language: 'pt_BR', language_url: '/assets/react/plugins/tinymce/langs/pt_BR.js',
        plugins: legacyTinyMcePlugins, toolbar,
        spellchecker_languages: 'Inglês=en,Dinamarquês=da,Holandês=nl,Finlandês=fi,Francês=fr_FR,Alemão=de,Italiano=it,Polonês=pl,Português (Brasil)=pt_BR,Espanhol=es,Sueco=sv', ...(styleFormats ? { style_formats: styleFormats } : {}),
        setup: (editor: TinyMceInstance): void => {
          instance = editor;
          editorRef.current = editor;
          // Search/replace in TinyMCE 4.2 creates a window outside windowManager.
          let previousWindows = new Set<Element>();
          editor.on('BeforeExecCommand', () => { previousWindows = new Set(Array.from(document.querySelectorAll('.mce-window'))); });
          editor.on('ExecCommand', () => {
            document.querySelectorAll<HTMLElement>('.mce-window').forEach(dialog => {
              if (!previousWindows.has(dialog)) auxiliaryWindows.add(dialog);
            });
          });
          editor.on('init', () => {
            if (cancelled) { editor.remove(); return; }
            editor.getContainer().setAttribute('aria-label', label);
            updateAccessibility(editor);
          });
          editor.on('input change undo redo', () => {
            if (!cancelled && editor.initialized) callback.current?.(editor.getContent());
          });
        }
      });
    }
    void initialize().catch((error: unknown) => {
      console.error('Não foi possível carregar o editor WYSIWYG.', error);
      if (!cancelled) failure.current?.();
    });
    return () => {
      cancelled = true;
      auxiliaryWindows.forEach(dialog => {
        if (dialog.isConnected) dialog.querySelector<HTMLElement>('.mce-close')?.click();
      });
      instance?.remove();
      editorRef.current = undefined;
      textarea?.remove();
    };
  }, [host, height, label, toolbar, styleFormats]);
}
