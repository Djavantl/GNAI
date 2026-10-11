export interface Contact {
  id: string;
  name: string;
  position: string;
  company: string;
  email?: string;
  avatar: string;
}
export interface ContactCardProps {
  contact: Contact;
  onEdit?: (contact: Contact) => void;
  onDelete?: (contact: Contact) => void;
}
