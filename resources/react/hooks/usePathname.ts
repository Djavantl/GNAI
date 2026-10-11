import { usePage } from '@inertiajs/react';

export function usePathname(): string {
  return new URL(usePage().url, window.location.origin).pathname;
}
