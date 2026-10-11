import type { SummernoteRuntime } from './SummernoteEditor.types';

/** The pinned 0.6 plugin does not detach its document drag handlers on destroy. */
export function trackSummernoteDocumentEvents(runtime: SummernoteRuntime, initialize: () => void): () => void {
  const before = Object.values(runtime._data(document, 'events') ?? {}).flat();
  initialize();
  const added = Object.entries(runtime._data(document, 'events') ?? {}).flatMap(([type, handlers]) => handlers.filter(handler => !before.includes(handler)).map(handler => ({ type, handler })));
  return () => added.forEach(({ type, handler }) => runtime(document).off(type, handler.handler));
}
