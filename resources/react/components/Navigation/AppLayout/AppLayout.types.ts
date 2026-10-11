import type { SidebarEntry } from '@/components/Navigation/SidebarPrimary/SidebarPrimary.types';
import type { NotificationItem } from '@/components/Navigation/TopBarPrimary/TopBarPrimary.data';

export interface AppLayoutProps {
  auth: { user: { name: string; photo_url: string } | null };
  navigation: { entries: SidebarEntry[]; currentItemKey: string | null };
  topbar: { institutionName: string; notifications: NotificationItem[]; unreadCount: number } | null;
  impersonating: boolean;
  [key: string]: unknown;
}
