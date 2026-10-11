export function displayDate(value: string): string {
  return value.replace(/^(\d{4})-(\d{2})-(\d{2})$/, '$3/$2/$1');
}

export function backendDate(value: string): string {
  // Incomplete or invalid dates still reach the server's existing validation.
  return value.replace(/^(\d{2})\/(\d{2})\/(\d{4})$/, '$3-$2-$1');
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
