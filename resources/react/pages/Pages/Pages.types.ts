import type { AccountPageKind } from '@/pages/AccountPages/AccountPages.types';
import type { ErrorCode } from '@/components/UI/ErrorState/ErrorState.types';
export type PageDefinition =
  | { key: string; kind: 'account'; account: AccountPageKind; standalone: true }
  | { key: string; kind: 'error'; code: ErrorCode; standalone: boolean }
  | { key: string; kind: 'starter'; standalone: false };
