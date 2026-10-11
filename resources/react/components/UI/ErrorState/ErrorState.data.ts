import type { ErrorCode, ErrorDescription } from './ErrorState.types';
export const errorDescriptions: Record<ErrorCode, ErrorDescription> = {
  '400': { title: 'Solicitação inválida', paragraphs: ['Seu navegador enviou uma solicitação inválida.'] },
  '403': { title: 'Acesso negado', paragraphs: ['Você não tem permissão para acessar este servidor.'] },
  '404': { title: 'Ops! Página não encontrada', paragraphs: ['Esta página não foi encontrada ou não está disponível.', 'Use o menu acima ou o botão abaixo para voltar.'] },
  '500': { title: 'Erro interno do servidor.', paragraphs: ['Tente atualizar a página ou entre em contato com o'], supportLink: true },
  '503': { title: 'Este site está em manutenção.', paragraphs: ['Volte em breve para conferir se nossa equipe já concluiu os ajustes.'] },
};

export const errorStateCopy = { support: 'suporte', home: 'Voltar ao início', label: 'Erro' };
