import { useCallback, useRef, useState } from 'react';
import Button from '@/components/UI/Button/Button';
import CardBox from '@/components/UI/CardBox/CardBox';
import NotificationToastLayer from '@/components/UI/NotificationToast/NotificationToastLayer';
import type { NotificationToastItem } from '@/components/UI/NotificationToast/NotificationToast';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  notificationSections,
  sampleNotificationText,
  type NotificationButtonItem,
  type NotificationSectionItem
} from './Notifications.data';
import './Notifications.css';

export function NotificationSection({
  section,
  onNotify
}: {
  section: NotificationSectionItem;
  onNotify: (button: NotificationButtonItem) => void;
}): React.JSX.Element {
  return (
    <div className="notificationsPage__section">
      <h4 className="notificationsPage__sectionTitle header-title m-t-0">
        <b>{section.title}</b>
      </h4>
      <p className="notificationsPage__description text-muted m-b-30 font-13">
        {section.description}{' '}
        <code>info</code>, <code>success</code>, <code>warning</code>, <code>error</code>, <code>black</code>, <code>white</code>.
      </p>

      <div className="button-list notificationsPage__buttonList">
        {section.buttons.flatMap((button: NotificationButtonItem) => [
          button.lineBreakBefore ? (
            <span
              key={`${section.title}-${button.label}-${button.position}-break`}
              className="notificationsPage__lineBreak"
            />
          ) : null,
          (
            <Button
              key={`${section.title}-${button.label}-${button.position}`}
              tone={button.buttonTone}
              onClick={() => onNotify(button)}
            >
              {button.label}
            </Button>
          )
        ])}
      </div>
    </div>
  );
}


export function NotificationsPageTitle(): React.JSX.Element {
  return (
    <div className="notificationsPage__pageTitleRow">
      <h4 className="page-title notificationsPage__pageTitle">Notificações</h4>
      <PageBreadcrumb
        items={[
          { label: 'Interface', href: '#' },
          { label: 'UI Kit', href: '#' },
          { label: 'Notificações' }
        ]}
      />
    </div>
  );
}

export function NotificationCards({ onNotify }: { onNotify: (button: NotificationButtonItem) => void }): React.JSX.Element {
  return (
    <>
      {[0, 2, 4].map((startIndex: number) => (
        <CardBox key={startIndex} className="notificationsPage__card">
          <div className="notificationsPage__row">
            {notificationSections.slice(startIndex, startIndex + 2).map((section: NotificationSectionItem) => (
              <div key={section.title} className="notificationsPage__col">
                <NotificationSection section={section} onNotify={onNotify} />
              </div>
            ))}
          </div>
        </CardBox>
      ))}
    </>
  );
}
