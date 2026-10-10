export const appPageTransitionStartEvent = 'bold:page-transition-start';
export const appPageTransitionReadyEvent = 'bold:page-transition-ready';

export function startAppPageTransition(): void {
  window.dispatchEvent(new Event(appPageTransitionStartEvent));
}

export function markAppPageTransitionReady(): void {
  window.dispatchEvent(new Event(appPageTransitionReadyEvent));
}
