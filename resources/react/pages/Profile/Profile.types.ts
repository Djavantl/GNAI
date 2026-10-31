export interface ProfilePerson {
  id: number;
  name: string;
  document: string | null;
  birth_date: string | null;
  gender: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  photo: string | null;
  photo_url: string;
}

export interface ProfileProfessional {
  id: number;
  registration: string | null;
  position?: { name: string } | null;
}

export interface ProfileTeacher {
  id: number;
  registration: string | null;
}

export interface ProfileEditProps {
  person: ProfilePerson;
  professional: ProfileProfessional | null;
  teacher: ProfileTeacher | null;
}

export interface AccountProfile {
  id: number;
  name: string;
  email: string;
  photo: string | null;
  photo_url: string;
  is_super_admin: boolean;
  is_admin: boolean;
}

export interface ProfileAccountProps {
  account: AccountProfile;
}
