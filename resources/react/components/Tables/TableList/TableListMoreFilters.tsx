import { useEffect, useId, useState, type ReactNode } from 'react';

import { joinClassNames } from '@/utils/joinClassNames';

interface TableListMoreFiltersProps {
  children: ReactNode;
  active?: boolean;
}

export default function TableListMoreFilters({
  children,
  active = false,
}: TableListMoreFiltersProps): React.JSX.Element {
  const [open, setOpen] = useState(active);
  const contentId = useId();

  useEffect(() => {
    if (active) {
      setOpen(true);
    }
  }, [active]);

  return (
    <section
      className={joinClassNames(
        'tableListMoreFilters',
        open && 'tableListMoreFilters--open',
      )}
    >
      <button
        type="button"
        className="tableListMoreFilters__trigger"
        aria-controls={contentId}
        aria-expanded={open}
        onClick={() => setOpen((current) => !current)}
      >
        <span className="tableListMoreFilters__chevron" aria-hidden="true" />
        Mais filtros
      </button>

      <div
        id={contentId}
        className="tableListMoreFilters__collapse"
        aria-hidden={!open}
      >
        <div className="tableListMoreFilters__content">{children}</div>
      </div>
    </section>
  );
}
