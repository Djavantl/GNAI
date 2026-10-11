export interface MailSummary {
  id: string;
  sender: string;
  subject: string;
  date: string;
  unread: boolean;
  starred: boolean;
  tone: string;
  attachment: boolean;
}
