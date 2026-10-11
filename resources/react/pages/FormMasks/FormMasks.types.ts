import type { NumericFormat } from '@/components/Forms/NumericInput/NumericInput.types';

interface MaskExampleBase { id: string; label: string; hint: string; }
export interface MaskExample extends MaskExampleBase { mask: string; }
export interface NumericExample extends MaskExampleBase { format: NumericFormat; }
