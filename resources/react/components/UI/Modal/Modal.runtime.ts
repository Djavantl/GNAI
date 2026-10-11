export type CustomboxPagePlaneName = 'Makeway' | 'Slip' | 'ContentScale' | 'Push';

const customboxFastOverlayEffects = new Set<string>([
  'fall',
  'sidefall',
  'blur',
  'flip',
  'sign',
  'superscaled',
  'rotate',
  'letmein',
  'makeway',
  'swell',
  'rotatedown',
  'flash'
]);

const customboxPagePlaneEffects = new Set<string>(['makeway', 'slip', 'contentscale', 'push']);
const customboxTogetherEffects = new Set<string>(['corner', 'slip', 'slidetogether', 'scale', 'door', 'contentscale', 'push']);

export function getCustomboxPagePlaneName(effect: string | undefined): CustomboxPagePlaneName | null {
  if (effect === 'makeway') return 'Makeway';
  if (effect === 'slip') return 'Slip';
  if (effect === 'contentscale') return 'ContentScale';
  if (effect === 'push') return 'Push';
  return null;
}

export function getCustomboxOverlaySpeed(effect: string | undefined): number {
  if (effect === 'slit') return 500;
  return effect && customboxFastOverlayEffects.has(effect) ? 100 : 200;
}

export function getCustomboxCloseDelay(effect: string | undefined): number {
  if (effect === 'slit') return 0;
  if (effect && customboxTogetherEffects.has(effect)) return 500;
  return 500 + getCustomboxOverlaySpeed(effect);
}

export function isCustomboxPagePlaneEffect(effect: string | undefined): boolean {
  return Boolean(effect && customboxPagePlaneEffects.has(effect));
}
