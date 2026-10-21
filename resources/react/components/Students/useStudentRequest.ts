import { notify } from '@/components/UI/NotificationToast/notify';

export function useStudentRequest() {
  const options = {
    onNetworkError: () => {
      notify({
        tone: 'error',
        title: 'Falha de conexão',
        text: 'Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.',
      });
      return false;
    },
    onHttpException: () => {
      notify({
        tone: 'error',
        title: 'Não foi possível concluir',
        text: 'Não foi possível concluir a operação. Atualize a página e tente novamente.',
      });
      return false;
    },
  };
  return { options };
}
