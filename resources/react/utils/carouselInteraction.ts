export function isInteractiveElement(target: EventTarget): boolean {
  return target instanceof Element && Boolean(target.closest('a, button, input, select, textarea, label, [role="button"]'));
}
