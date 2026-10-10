/** Preserve the legacy fullscreen geometry without its global jQuery.off('resize'). */
export function bindSummernoteFullscreen(root: HTMLElement): () => void {
  let restore: (() => void) | undefined;
  function toggle(event: MouseEvent): void {
    const button = (event.target as HTMLElement).closest<HTMLButtonElement>('[data-event="fullscreen"]');
    const editor = root.querySelector<HTMLElement>('.note-editor');
    if (!button || !editor) return;
    event.preventDefault();
    event.stopPropagation();
    if (restore) { restore(); restore = undefined; return; }
    const panels = Array.from(editor.querySelectorAll<HTMLElement>('.note-editable, .note-codable'));
    const heights = panels.map(panel => panel.style.height);
    const htmlOverflow = document.documentElement.style.overflow;
    const bodyOverflow = document.body.style.overflow;
    function resize(): void {
      const height = window.innerHeight - (editor!.querySelector('.note-toolbar')?.getBoundingClientRect().height ?? 0);
      panels.forEach(panel => { panel.style.height = `${height}px`; });
    }
    editor.classList.add('fullscreen');
    button.classList.add('active');
    document.documentElement.style.overflow = document.body.style.overflow = 'hidden';
    window.addEventListener('resize', resize);
    resize();
    restore = (): void => {
      window.removeEventListener('resize', resize);
      editor.classList.remove('fullscreen');
      button!.classList.remove('active');
      panels.forEach((panel, index) => { panel.style.height = heights[index]; });
      document.documentElement.style.overflow = htmlOverflow;
      document.body.style.overflow = bodyOverflow;
    };
  }
  root.addEventListener('click', toggle, true);
  return () => { restore?.(); root.removeEventListener('click', toggle, true); };
}
