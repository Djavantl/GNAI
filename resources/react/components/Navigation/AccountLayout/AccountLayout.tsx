import { useEffect, type ReactNode } from 'react';
import FlashNotifications from '@/components/UI/FlashNotifications/FlashNotifications';
import { initWaves } from '@/utils/initWaves';
import './AccountLayout.css';

export default function AccountLayout({ children, wide = false, illustration }: {
  children: ReactNode;
  wide?: boolean;
  illustration?: ReactNode;
}): React.JSX.Element {
  useEffect(() => { initWaves(); }, []);
  if (illustration) {
    return <>
      <main className="accountLayout accountLayout--split" aria-label="Páginas">
        <div className="accountLayout__splitPanel">
          <aside className="accountLayout__splitBrand">{illustration}</aside>
          <section className="accountLayout__splitContent">{children}</section>
        </div>
      </main>
      <FlashNotifications />
    </>;
  }
  return <>
    <main className="accountLayout" aria-label="Páginas">
      <div className="accountLayout__background" aria-hidden="true" />
      <div className={wide ? 'accountLayout__container' : undefined}>
        <div className={wide ? 'accountLayout__row' : undefined}>
          <div className={wide ? 'accountLayout__column' : undefined}>
            <div className={`accountLayout__wrapper${wide ? ' accountLayout__wrapper--wide' : ''}`}>
              {children}
            </div>
          </div>
        </div>
      </div>
    </main>
    <FlashNotifications />
  </>;
}
