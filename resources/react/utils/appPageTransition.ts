export const appPageTransitionStartEvent = 'react:page-transition-start';
export const appPageTransitionReadyEvent = 'react:page-transition-ready';

export function startAppPageTransition(): void {
  window.dispatchEvent(new Event(appPageTransitionStartEvent));
}

export function markAppPageTransitionReady(): void {
  window.dispatchEvent(new Event(appPageTransitionReadyEvent));
}
