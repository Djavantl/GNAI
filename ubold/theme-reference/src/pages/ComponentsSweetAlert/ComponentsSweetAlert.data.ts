import type { SweetAlertExample } from './ComponentsSweetAlert.types';

export const sweetAlertExamples: SweetAlertExample[] = [
  { id: 'basic', label: 'Exemplo básico', buttonLabel: 'Clique aqui' },
  { id: 'title', label: 'Um título com texto abaixo', buttonLabel: 'Clique aqui' },
  { id: 'success', label: 'Uma mensagem de sucesso!', buttonLabel: 'Clique aqui' },
  { id: 'warning', label: 'Uma mensagem de aviso, com uma função vinculada ao botão "Confirmar"...', buttonLabel: 'Clique aqui' },
  { id: 'params', label: 'Ao passar um parâmetro, você pode executar outra ação no "Cancelar".', buttonLabel: 'Clique aqui' },
  { id: 'image', label: 'Uma mensagem com cabeçalho de imagem customizado', buttonLabel: 'Clique aqui' },
  { id: 'timer', label: 'Uma mensagem com fechamento automático', buttonLabel: 'Clique aqui' }
];

export const sweetAlertLongText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis';
