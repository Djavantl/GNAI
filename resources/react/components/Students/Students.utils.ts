export const aeeBase = '/specialized-educational-support';
export const studentsBase = `${aeeBase}/students`;
export const studentUrl = (id: number, action = 'show'): string => `${studentsBase}/${id}${action ? `/${action}` : ''}`;
export const studentRecordUrl = (id: number, tab: string): string => {
  const query = new URLSearchParams({ tab });
  return `${studentUrl(id)}?${query.toString()}`;
};
export function dateLabel(value: string | null | undefined): string {
  if (!value) return '---';
  const [year, month, day] = value.slice(0, 10).split('-');
  return day && month && year ? `${day}/${month}/${year}` : '---';
}
export function dateTimeLabel(value: string | null | undefined): string {
  if (!value) return '---';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return dateLabel(value);
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  }).format(date);
}
export function fileSizeLabel(bytes: number | null | undefined): string {
  if (bytes == null || Number.isNaN(bytes)) return '---';
  return `${(bytes / 1024 / 1024).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} MB`;
}
export function cpfMask(value: string): string {
  return value.replace(/\D/g, '').slice(0, 11).replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}
export function phoneMask(value: string): string {
  const digits = value.replace(/\D/g, '').slice(0, 11);
  if (digits.length <= 2) return digits ? `(${digits}` : '';
  const number = digits.slice(2);
  const split = digits.length > 10 ? 5 : 4;
  return `(${digits.slice(0, 2)}) ${number.slice(0, split)}${number.length > split ? `-${number.slice(split)}` : ''}`;
}
