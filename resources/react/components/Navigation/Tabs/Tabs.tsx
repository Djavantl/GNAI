import {
  useEffect,
  useId,
  useRef,
  useState,
  type KeyboardEvent,
  type ReactNode,
} from 'react';
import './Tabs.css';

export interface TabItem {
  id: string;
  label: ReactNode;
  accessibleLabel?: string;
  icon?: string;
  content: ReactNode;
}

interface TabsProps {
  items: TabItem[];
  initialActiveId?: string;
  variant?: 'standard' | 'material' | 'bootstrap' | 'justified' | 'vertical' | 'vertical-reverse' | 'pills';
}

export default function Tabs({ items, initialActiveId, variant = 'standard' }: TabsProps): React.JSX.Element {
  const [activeId, setActiveId] = useState<string>(initialActiveId ?? items[0]?.id ?? '');
  const activeItem = items.find((item: TabItem) => item.id === activeId) ?? items[0];
  const activeIndex = Math.max(0, items.findIndex((item: TabItem) => item.id === activeId));
  const isVertical = variant === 'vertical' || variant === 'vertical-reverse';
  const hasMobileIcons = items.some((item: TabItem) => item.icon);
  const tabsId = useId();
  const previousInitialActiveId = useRef(initialActiveId);

  useEffect(() => {
    const initialActiveIdChanged = previousInitialActiveId.current
      !== initialActiveId;
    previousInitialActiveId.current = initialActiveId;

    if (
      initialActiveIdChanged
      && initialActiveId
      && initialActiveId !== activeId
      && items.some((item) => item.id === initialActiveId)
    ) {
      setActiveId(initialActiveId);
      return;
    }

    if (items.length && !items.some((item) => item.id === activeId)) {
      setActiveId(items[0].id);
    }
  }, [activeId, initialActiveId, items]);

  function selectTab(id: string): void {
    setActiveId(id);
  }

  function handleKeyDown(
    event: KeyboardEvent<HTMLButtonElement>,
    currentIndex: number,
  ): void {
    const previousKey = isVertical ? 'ArrowUp' : 'ArrowLeft';
    const nextKey = isVertical ? 'ArrowDown' : 'ArrowRight';
    let nextIndex: number | undefined;

    if (event.key === previousKey) {
      nextIndex = (currentIndex - 1 + items.length) % items.length;
    } else if (event.key === nextKey) {
      nextIndex = (currentIndex + 1) % items.length;
    } else if (event.key === 'Home') {
      nextIndex = 0;
    } else if (event.key === 'End') {
      nextIndex = items.length - 1;
    }

    if (nextIndex === undefined) return;

    event.preventDefault();
    selectTab(items[nextIndex].id);
    const nextTab = event.currentTarget
      .closest('[role="tablist"]')
      ?.querySelectorAll<HTMLButtonElement>('[role="tab"]')
      .item(nextIndex);
    nextTab?.focus();
  }

  const navigation = (
    <ul
      className={`tabs__nav ${isVertical ? 'tabs__nav--vertical' : ''} ${variant === 'material' ? 'tabs__nav--material' : ''} ${variant === 'bootstrap' ? 'tabs__nav--bootstrap' : ''} ${variant === 'justified' ? 'tabs__nav--justified' : ''} ${variant === 'pills' ? 'tabs__nav--pills' : ''} ${variant === 'standard' || variant === 'material' ? 'tabs__nav--animated' : ''} ${hasMobileIcons ? 'tabs__nav--mobileIcons' : ''}`}
      role="tablist"
      aria-orientation={isVertical ? 'vertical' : 'horizontal'}
    >
      {items.map((item: TabItem, index) => (
        <li key={item.id} className={activeId === item.id ? 'tabs__item--active' : ''} role="presentation">
          <button
            type="button"
            className="tabs__button"
            role="tab"
            id={`${tabsId}-${item.id}-tab`}
            aria-label={item.accessibleLabel}
            aria-selected={activeId === item.id}
            aria-controls={`${tabsId}-${item.id}-panel`}
            tabIndex={activeId === item.id ? 0 : -1}
            onClick={() => selectTab(item.id)}
            onKeyDown={(event) => handleKeyDown(event, index)}
          >
            <span className="tabs__label">{item.label}</span>
            {item.icon ? <i className={`tabs__icon ${item.icon}`} aria-hidden="true" /> : null}
          </button>
        </li>
      ))}
      {variant === 'standard' || variant === 'material' ? (
        <span
          className="tabs__indicator"
          style={{
            left: `${(activeIndex / items.length) * 100}%`,
            right: `${((items.length - activeIndex - 1) / items.length) * 100}%`,
          }}
          aria-hidden="true"
        />
      ) : null}
    </ul>
  );

  if (isVertical) {
    return (
      <div className="tabs__verticalEnv">
        {variant === 'vertical' ? navigation : null}
        <div
          id={`${tabsId}-${activeItem?.id}-panel`}
          className="tabs__content tabs__content--vertical"
          role="tabpanel"
          aria-labelledby={`${tabsId}-${activeItem?.id}-tab`}
          tabIndex={0}
        >
          {activeItem?.content}
        </div>
        {variant === 'vertical-reverse' ? navigation : null}
      </div>
    );
  }

  return (
    <div className="tabs">
      {navigation}
      <div
        id={`${tabsId}-${activeItem?.id}-panel`}
        className="tabs__content"
        role="tabpanel"
        aria-labelledby={`${tabsId}-${activeItem?.id}-tab`}
        tabIndex={0}
      >
        {activeItem?.content}
      </div>
    </div>
  );
}

export interface AccordionItem {
  id: string;
  title: string;
  content: ReactNode;
}

interface AccordionProps {
  items: AccordionItem[];
  initialActiveId?: string;
  joined?: boolean;
}

export function Accordion({ items, initialActiveId, joined = false }: AccordionProps): React.JSX.Element {
  const [activeId, setActiveId] = useState<string | null>(initialActiveId ?? null);

  return (
    <div className={`tabs__accordion ${joined ? 'tabs__accordion--joined' : ''}`}>
      {items.map((item: AccordionItem) => {
        const isOpen = activeId === item.id;
        return (
          <div className={`tabs__accordionItem ${isOpen ? 'tabs__accordionItem--open' : ''}`} key={item.id}>
            <h4 className="tabs__accordionHeading">
              <button type="button" aria-expanded={isOpen} onClick={() => setActiveId(isOpen ? null : item.id)}>
                {item.title}
              </button>
            </h4>
            <div className={`tabs__accordionBody ${isOpen ? 'tabs__accordionBody--open' : ''}`} aria-hidden={!isOpen}>
              <div className="tabs__accordionBodyInner">{item.content}</div>
            </div>
          </div>
        );
      })}
    </div>
  );
}
