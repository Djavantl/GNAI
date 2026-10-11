import { useState, type ReactNode } from 'react';
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

  function selectTab(id: string): void {
    setActiveId(id);
  }

  const navigation = (
    <ul
      className={`tabs__nav ${isVertical ? 'tabs__nav--vertical' : ''} ${variant === 'material' ? 'tabs__nav--material' : ''} ${variant === 'bootstrap' ? 'tabs__nav--bootstrap' : ''} ${variant === 'justified' ? 'tabs__nav--justified' : ''} ${variant === 'pills' ? 'tabs__nav--pills' : ''} ${variant === 'standard' || variant === 'material' ? 'tabs__nav--animated' : ''} ${hasMobileIcons ? 'tabs__nav--mobileIcons' : ''}`}
      role="tablist"
    >
      {items.map((item: TabItem) => (
        <li key={item.id} className={activeId === item.id ? 'tabs__item--active' : ''} role="presentation">
          <button
            type="button"
            className="tabs__button"
            role="tab"
            aria-label={item.accessibleLabel}
            aria-selected={activeId === item.id}
            onClick={() => selectTab(item.id)}
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
        <div className="tabs__content tabs__content--vertical" role="tabpanel">{activeItem?.content}</div>
        {variant === 'vertical-reverse' ? navigation : null}
      </div>
    );
  }

  return (
    <div className="tabs">
      {navigation}
      <div className="tabs__content" role="tabpanel">{activeItem?.content}</div>
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
