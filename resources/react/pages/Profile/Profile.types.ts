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
