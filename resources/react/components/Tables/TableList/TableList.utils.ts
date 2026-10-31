import type { TableListRange } from './TableList.types';

/** Texto padrão do intervalo das listagens: "1 a 10 de 14". */
export function formatTableListRange({ from, to, total }: TableListRange): string {
  return `${from ?? 0} a ${to ?? 0} de ${total}`;
}

/** Datas sem horário não devem mudar de dia por causa do fuso do navegador. */
export function formatTableListDate(value: string | null | undefined, withTime = false): string {
  if (!value) return 'Não informado';
  const date = new Date(/^\d{4}-\d{2}-\d{2}$/.test(value) ? `${value}T00:00:00` : value);
  if (Number.isNaN(date.getTime())) return 'Não informado';
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    ...(withTime ? { timeStyle: 'short' as const } : {}),
  }).format(date);
}

/** Resumo textual de editores, sem montar HTML ou imagens na tabela. */
export function formatTableListText(value: string | null | undefined): string {
  if (!value) return 'Não informado';
  const html = value.replace(/<\/(?:p|div|li|h[1-6])>|<br\s*\/?\s*>/gi, ' ');
  if (typeof document === 'undefined') {
    return html.replace(/<[^>]*>/g, '').trim() || 'Não informado';
  }
  const template = document.createElement('template');
  template.innerHTML = html;
  template.content.querySelectorAll('script, style').forEach((node) => node.remove());
  return template.content.textContent?.replace(/\s+/g, ' ').trim() || 'Não informado';
}
