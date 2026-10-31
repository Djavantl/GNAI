import type { TableListRange } from './TableList.types';

/** Texto padrão do intervalo das listagens: "1–10 de 14". */
export function formatTableListRange({ from, to, total }: TableListRange): string {
  return `${from ?? 0}–${to ?? 0} de ${total}`;
}
