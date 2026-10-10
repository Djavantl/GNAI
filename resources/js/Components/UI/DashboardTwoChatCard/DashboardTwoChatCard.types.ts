export interface DashboardTwoChatMessage {
  author: string;
  avatar: string;
  id: string;
  isCurrentUser?: boolean;
  text: string;
  time: string;
}

export interface DashboardTwoChatCurrentUser {
  author: string;
  avatar: string;
  isCurrentUser?: boolean;
  timeFormatter?: () => string;
}
