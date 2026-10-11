export type CredentialsMode = 'signin' | 'signup';
export interface CredentialsCopy {
  username: string;
  password: string;
  email: string;
  remember: string;
  termsLead: string;
  terms: string;
  submit: string;
  compactSubmit: string;
  forgot: string;
  socialTitle: string;
}
