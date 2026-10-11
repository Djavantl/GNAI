import type { CredentialsCopy, CredentialsMode } from './CredentialsForm.types';

export const credentialsCopy: Record<CredentialsMode, CredentialsCopy> = {
  signin: {
    username: 'Usuário', password: 'Senha', email: 'E-mail', remember: 'Lembrar de mim',
    termsLead: 'Aceito os', terms: 'Termos e Condições', submit: 'Entrar', compactSubmit: 'Entrar',
    forgot: 'Esqueceu sua senha?', socialTitle: 'Entrar com',
  },
  signup: {
    username: 'Usuário', password: 'Senha', email: 'E-mail', remember: 'Lembrar de mim',
    termsLead: 'Aceito os', terms: 'Termos e Condições', submit: 'Cadastrar', compactSubmit: 'Cadastrar',
    forgot: 'Esqueceu sua senha?', socialTitle: 'Cadastrar com',
  },
};
