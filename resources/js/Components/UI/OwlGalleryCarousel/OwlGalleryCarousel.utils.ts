export const galleryGap = 20;
export const galleryTransitionDuration = 250;

export function getVisibleItems(viewportWidth: number): number {
  if (viewportWidth >= 1100) {
    return 5;
  }

  if (viewportWidth >= 1000) {
    return 3;
  }

  if (viewportWidth >= 700) {
    return 4;
  }

  if (viewportWidth >= 480) {
    return 2;
  }

  return 1;
}

export function normalizeIndex(index: number, length: number): number {
  return ((index % length) + length) % length;
}
