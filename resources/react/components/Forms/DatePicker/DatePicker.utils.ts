export function displayDate(value: string): string {
  return value.replace(/^(\d{4})-(\d{2})-(\d{2})$/, '$3/$2/$1');
}

export function backendDate(value: string): string {
  const match = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(value);
  if (!match) {
    return value;
  }
  if (!isValidDateParts(Number(match[3]), Number(match[2]), Number(match[1]))) return '';
  return `${match[3]}-${match[2]}-${match[1]}`;
}

export function maskDate(value: string): string {
  const digits = value.replace(/\D/g, '').slice(0, 8);
  return [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4)].filter(Boolean).join('/');
}

export function dateCaret(value: string, digitCount: number): number {
  if (digitCount <= 0) return 0;
  let count = 0;
  for (let index = 0; index < value.length; index++) {
    if (/\d/.test(value[index])) count++;
    if (count === digitCount) return index + 1;
  }
  return value.length;
}

export function isValidDateParts(year: number, month: number, day: number): boolean {
  if (!Number.isInteger(year) || year < 1 || month < 1 || month > 12 || day < 1) {
    return false;
  }

  const leapYear = year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0);
  const daysPerMonth = [31, leapYear ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
  return day <= daysPerMonth[month - 1];
}

export function isValidDatePrefix(value: string): boolean {
  const digits = value.replace(/\D/g, '');
  if (digits.length >= 2) {
    const day = Number(digits.slice(0, 2));
    if (day < 1 || day > 31) return false;
  }
  if (digits.length >= 4) {
    const month = Number(digits.slice(2, 4));
    if (month < 1 || month > 12) return false;
  }
  if (digits.length === 8) {
    return isValidDateParts(
      Number(digits.slice(4, 8)),
      Number(digits.slice(2, 4)),
      Number(digits.slice(0, 2)),
    );
  }
  return digits.length < 8;
}
