import { useEffect, useRef, useState } from 'react';

export function compactListFilters<T extends Record<string, unknown>>(
  values: T,
): Partial<T> {
  return Object.fromEntries(
    Object.entries(values).filter(([, value]) => value !== ''),
  ) as Partial<T>;
}

interface AutoListFiltersOptions<T> {
  initialValues: T;
  emptyValues: T;
  onSearch: (values: T) => void;
  delay?: number;
}

export default function useAutoListFilters<T extends Record<string, string>>({
  initialValues,
  emptyValues,
  onSearch,
  delay = 500,
}: AutoListFiltersOptions<T>) {
  const [values, setValues] = useState<T>(initialValues);
  const timer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);
  const onSearchRef = useRef(onSearch);
  onSearchRef.current = onSearch;

  function cancelScheduled(): void {
    clearTimeout(timer.current);
    timer.current = undefined;
  }

  useEffect(() => {
    cancelScheduled();
    setValues(initialValues);
  }, [initialValues]);

  useEffect(() => {
    return cancelScheduled;
  }, []);

  function search(next: T): void {
    cancelScheduled();
    onSearchRef.current(next);
  }

  function update(name: keyof T, value: string): void {
    const next = { ...values, [name]: value };
    setValues(next);
    cancelScheduled();
    timer.current = setTimeout(
      () => onSearchRef.current(next),
      delay,
    );
  }

  function clear(): void {
    const next = { ...emptyValues };
    setValues(next);
    search(next);
  }

  function submit(): void {
    search(values);
  }

  return { values, update, clear, submit, cancelScheduled };
}
