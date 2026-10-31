import { Link } from "@inertiajs/react";
import "@/components/Navigation/Tabs/Tabs.css";
import "./PageTabs.css";

export interface PageTabItem<Key extends string = string> {
    key: Key;
    label: string;
    href: string;
    iconClass: string;
}

export default function PageTabs<Key extends string>({
    items,
    activeKey,
    ariaLabel,
}: {
    items: ReadonlyArray<PageTabItem<Key>>;
    activeKey: Key;
    ariaLabel: string;
}): React.JSX.Element {
    const activeIndex = Math.max(0, items.findIndex((item) => item.key === activeKey));

    return (
        <nav className="pageTabs" aria-label={ariaLabel}>
            <ul className="tabs__nav--animated">
                {items.map((item) => (
                    <li key={item.key}>
                        <Link
                            href={item.href}
                            preserveScroll
                            className={
                                item.key === activeKey
                                    ? "pageTabs__link pageTabs__link--active"
                                    : "pageTabs__link"
                            }
                            aria-current={item.key === activeKey ? "page" : undefined}
                        >
                            <i className={item.iconClass} aria-hidden="true" />
                            <span>{item.label}</span>
                        </Link>
                    </li>
                ))}
                {/* Em layouts persistentes, o indicador desliza entre as abas. */}
                <li
                    className="tabs__indicator"
                    style={{
                        left: `${(activeIndex / items.length) * 100}%`,
                        right: `${((items.length - activeIndex - 1) / items.length) * 100}%`,
                    }}
                    aria-hidden="true"
                />
            </ul>
        </nav>
    );
}
