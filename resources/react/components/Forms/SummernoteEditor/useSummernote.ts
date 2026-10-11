import { useEffect, useRef, type RefObject } from 'react';
import { loadExternalScript } from '@/utils/loadExternalScript';
import { bindSummernoteFullscreen } from './SummernoteEditor.fullscreen';
import { trackSummernoteDocumentEvents } from './SummernoteEditor.events';
import { completeSummernoteLocale } from './SummernoteEditor.locale';
import type { SummernoteCollection, SummernoteEditorProps, SummernoteRuntime } from './SummernoteEditor.types';

export function useSummernote(host: RefObject<HTMLDivElement | null>, { defaultValue = '', mode = 'standard', height = 350, label = 'Editor de texto', onChange }: SummernoteEditorProps): void {
  const initialValue = useRef(defaultValue);
  const callback = useRef(onChange);
  callback.current = onChange;
  useEffect(() => {
    let cancelled = false;
    let editor: SummernoteCollection | undefined;
    let runtime: SummernoteRuntime | undefined;
    let overlay: HTMLDivElement | undefined;
    let holder: HTMLDivElement | undefined;
    const root = host.current;
    let cleanupFullscreen: (() => void) | undefined;
    let cleanupDocumentEvents: (() => void) | undefined;
    async function initialize(): Promise<void> {
      // Reuse the existing jQuery/Bootstrap interaction runtime already used by FormPickers.
      for (const src of ['/assets/react/js/jquery.min.js', '/assets/react/js/bootstrap.min.js', '/assets/react/plugins/summernote/summernote.min.js', '/assets/react/plugins/summernote/summernote-pt-BR.js']) await loadExternalScript(src);
      if (cancelled || !root) return;
      runtime = (window as Window & { jQuery?: SummernoteRuntime }).jQuery;
      if (!runtime) throw new Error('jQuery indisponível.');
      completeSummernoteLocale(runtime);
      holder = document.createElement('div');
      holder.innerHTML = initialValue.current;
      root.append(holder);
      overlay = document.createElement('div');
      overlay.className = 'summernoteOverlay';
      overlay.style.setProperty('--summernote-accent', getComputedStyle(root).getPropertyValue('--summernote-accent'));
      document.body.append(overlay);
      const oldAirNodes = new Set(Array.from(document.querySelectorAll('body > .note-air-layout')));
      editor = runtime(holder);
      cleanupDocumentEvents = trackSummernoteDocumentEvents(runtime, () => editor!.summernote({ lang: 'pt-BR', height: mode === 'standard' ? height : undefined, airMode: mode === 'inline', focus: false, onChange: (html: string): void => callback.current?.(html) }));
      document.querySelectorAll('body > .note-air-layout').forEach(node => { if (!oldAirNodes.has(node)) overlay!.append(node); });
      const containers = [root, overlay];
      for (const container of containers) {
        container.querySelectorAll<HTMLAnchorElement>('.note-help-dialog a').forEach(link => {
          if (link.textContent === 'Project') link.textContent = 'Projeto';
          if (link.textContent === 'Issues') link.textContent = 'Relatar problemas';
        });
        container.querySelectorAll<HTMLElement>('.note-editable').forEach(element => { element.setAttribute('role', 'textbox'); element.setAttribute('aria-multiline', 'true'); element.setAttribute('aria-label', label); });
        container.querySelectorAll<HTMLElement>('button').forEach(button => {
          const title = button.getAttribute('data-original-title');
          if (title) button.setAttribute('aria-label', title);
          const tooltip = runtime!(button).data('bs.tooltip') as { options: { container: HTMLElement } } | undefined;
          if (tooltip) tooltip.options.container = overlay!;
        });
        container.querySelectorAll<HTMLElement>('.modal').forEach(modal => {
          modal.setAttribute('role', 'dialog');
          modal.setAttribute('aria-label', modal.querySelector('.modal-title, .title')?.textContent ?? 'Diálogo do editor');
          modal.querySelector('.close')?.setAttribute('aria-label', 'Fechar');
          modal.querySelector('.close')?.removeAttribute('aria-hidden');
          modal.querySelectorAll<HTMLInputElement>('input').forEach((input, index) => {
            input.id = `${root.id}-${mode}-${modal.classList[0]}-${index}`;
            const labelElement = input.parentElement?.querySelector('label');
            if (labelElement) labelElement.htmlFor = input.id;
          });
          runtime!(modal).on('shown.bs.modal.summernoteReact', () => {
            document.querySelectorAll('.modal-backdrop:not(.summernoteBackdrop)').forEach(backdrop => backdrop.classList.add('summernoteBackdrop'));
          });
        });
      }
      root.querySelector<HTMLTextAreaElement>('.note-codable')?.setAttribute('aria-label', 'Código-fonte HTML');
      cleanupFullscreen = bindSummernoteFullscreen(root);
    }
    void initialize().catch((error: unknown) => console.error('Não foi possível carregar o Summernote.', error));
    return () => {
      cancelled = true;
      cleanupFullscreen?.();
      cleanupDocumentEvents?.();
      if (runtime) for (const container of [root, overlay]) {
        container?.querySelectorAll('.modal').forEach(modal => { runtime!(modal).modal('hide'); runtime!(modal).off('.summernoteReact'); });
        container?.querySelectorAll('button').forEach(button => { if (runtime!(button).data('bs.tooltip')) runtime!(button).tooltip('destroy'); });
      }
      editor?.destroy();
      holder?.remove();
      overlay?.remove();
    };
  }, [host, mode, height, label]);
}
