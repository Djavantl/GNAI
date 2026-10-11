import { useState } from 'react';

export function useStudentRequest() {
  const [error, setError] = useState<string | null>(null);
  const options = {
    onBefore: () => { setError(null); },
    onNetworkError: () => { setError('Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.'); return false; },
    onHttpException: () => { setError('Não foi possível concluir a operação. Atualize a página e tente novamente.'); return false; },
  };
  return { error, options };
}
