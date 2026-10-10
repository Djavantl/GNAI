// The bundled legacy libraries expose dynamic, version-specific browser APIs.
export const chartWindow = window as Window & Record<string, any>;
