export interface MaskedTime {
  value: string;
  valid: boolean;
  complete: boolean;
}

function isValidHour(hour: string, useMeridian: boolean): boolean {
  if (!hour) return true;
  if (hour.length === 1) return true;
  if (hour.length !== 2) return false;

  const value = Number(hour);
  return useMeridian ? value >= 1 && value <= 12 : value >= 0 && value <= 23;
}

function isValidMinute(minute: string): boolean {
  if (!minute) return true;
  if (minute.length === 1) return Number(minute) <= 5;
  return minute.length === 2 && Number(minute) <= 59;
}

export function maskTime(value: string, useMeridian = false): MaskedTime {
  const source = value.trim().toUpperCase();
  const meridianMatch = useMeridian ? /\s*(AM|PM|A|P)$/.exec(source) : null;
  const meridian = meridianMatch?.[1]?.startsWith('A') ? 'AM' : meridianMatch ? 'PM' : '';
  const withoutMeridian = meridianMatch
    ? source.slice(0, meridianMatch.index).trim()
    : source;
  const colonIndex = withoutMeridian.indexOf(':');
  const hasColon = colonIndex >= 0;
  let hour: string;
  let minute: string;

  if (hasColon) {
    const parts = withoutMeridian.split(':');
    if (parts.length > 2) return { value: '', valid: false, complete: false };
    hour = parts[0].replace(/\D/g, '');
    minute = (parts[1] ?? '').replace(/\D/g, '');
  } else {
    const digits = withoutMeridian.replace(/\D/g, '').slice(0, 4);
    hour = digits.slice(0, 2);
    minute = digits.slice(2, 4);
  }

  if (hour.length > 2 || minute.length > 2 || !isValidHour(hour, useMeridian) || !isValidMinute(minute)) {
    return { value: '', valid: false, complete: false };
  }

  const hasMinutePart = hasColon || minute.length > 0;
  const hourValue = Number(hour);
  const completeTime = hour.length > 0
    && (hour.length === 2 || hasColon)
    && minute.length === 2
    && isValidMinute(minute)
    && (!useMeridian || Boolean(meridian));
  if (completeTime && useMeridian && (hourValue < 1 || hourValue > 12)) {
    return { value: '', valid: false, complete: false };
  }

  let formatted = hour;
  if (hasMinutePart) formatted += `:${minute}`;
  if (completeTime) {
    formatted = `${hour.padStart(2, '0')}:${minute}`;
    if (useMeridian) formatted += ` ${meridian}`;
  }

  return { value: formatted, valid: true, complete: completeTime };
}

export function timeCaret(value: string, digitCount: number, atEnd = false): number {
  if (atEnd) return value.length;
  if (digitCount <= 0) return 0;

  let count = 0;
  for (let index = 0; index < value.length; index += 1) {
    if (/\d/.test(value[index])) count += 1;
    if (count === digitCount) return index + 1;
  }
  return value.length;
}
