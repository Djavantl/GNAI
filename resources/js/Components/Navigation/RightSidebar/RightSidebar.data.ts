export interface ContactItem {
  avatar: string;
  name: string;
  status: 'online' | 'away' | 'offline';
}

export const contacts: ContactItem[] = [
  { avatar: '/assets/gnai/images/users/avatar-1.jpg', name: 'Chadengle', status: 'online' },
  { avatar: '/assets/gnai/images/users/avatar-2.jpg', name: 'Tomaslau', status: 'online' },
  { avatar: '/assets/gnai/images/users/avatar-3.jpg', name: 'Stillnotdavid', status: 'online' },
  { avatar: '/assets/gnai/images/users/avatar-4.jpg', name: 'Kurafire', status: 'online' },
  { avatar: '/assets/gnai/images/users/avatar-5.jpg', name: 'Shahedk', status: 'away' },
  { avatar: '/assets/gnai/images/users/avatar-6.jpg', name: 'Adhamdannaway', status: 'away' },
  { avatar: '/assets/gnai/images/users/avatar-7.jpg', name: 'Ok', status: 'away' },
  { avatar: '/assets/gnai/images/users/avatar-8.jpg', name: 'Arashasghari', status: 'offline' },
  { avatar: '/assets/gnai/images/users/avatar-9.jpg', name: 'Joshaustin', status: 'offline' },
  { avatar: '/assets/gnai/images/users/avatar-10.jpg', name: 'Sortino', status: 'offline' }
];
