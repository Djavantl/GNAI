import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import { notify } from '@/components/UI/NotificationToast/notify';
import { compactListFilters } from '@/hooks/useAutoListFilters';

type QueryValue = string | number;

const CONNECTION_FAILURE = {
  tone: 'error' as const,
  title: 'Falha de conexão',
  text: 'Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.',
};

function notifyFailure(text: string): void {
  notify({ tone: 'error', title: 'Não foi possível concluir', text });
}

interface TableListVisitOptions {
  /** Endereço da listagem. */
  url: string;
  /** Mensagem do toast quando o servidor falha ao atualizar a listagem. */
  failureText: string;
  /** Nome do parâmetro de página, quando o controller usa outro (ex.: `pedagogical_page`). */
  pageParam?: string;
}

/**
 * Recarga das listagens por visitas Inertia: filtros, paginação e limpeza.
 *
 * Uma visita nova cancela a anterior, para o filtro digitado durante uma requisição não ser descartado. Os
 * campos não ficam bloqueados durante a recarga; `loading` alimenta `TableListCard.loading` e a paginação.
 */
export function useTableListVisit({
  url,
  failureText,
  pageParam = 'page',
}: TableListVisitOptions) {
  const [loading, setLoading] = useState(false);
  const cancelCurrent = useRef<(() => void) | undefined>(undefined);

  useEffect(() => () => cancelCurrent.current?.(), []);

  function visitUrl(target: string, data: Record<string, QueryValue> = {}): void {
    cancelCurrent.current?.();
    router.get(target, data, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onCancelToken: (token) => {
        cancelCurrent.current = () => token.cancel();
      },
      onStart: () => setLoading(true),
      onNetworkError: () => {
        notify(CONNECTION_FAILURE);
        return false;
      },
      onHttpException: () => {
        notifyFailure(failureText);
        return false;
      },
      onFinish: () => {
        cancelCurrent.current = undefined;
        setLoading(false);
      },
    });
  }

  /** Recarrega a listagem com os filtros (sem os vazios) e a página pedida. */
  function visit(
    filters: Record<string, string> = {},
    page = 1,
    extra: Record<string, QueryValue> = {},
  ): void {
    visitUrl(url, {
      ...(compactListFilters(filters) as Record<string, string>),
      ...extra,
      ...(page > 1 ? { [pageParam]: page } : {}),
    });
  }

  function cancel(): void {
    cancelCurrent.current?.();
  }

  return { loading, visit, visitUrl, cancel };
}

interface TableListActionOptions {
  /** Mensagem do toast quando o servidor falha ao executar a ação. */
  failureText: string;
}

/**
 * Ação confirmada sobre um registro da listagem (excluir, ativar, concluir): guarda o registro escolhido para o
 * `ConfirmActionModal`, impede envio duplicado e apresenta falhas no toast. O sucesso vem do flash do backend.
 */
export function useTableListAction<T>({ failureText }: TableListActionOptions) {
  const [target, setTarget] = useState<T | null>(null);
  const [processing, setProcessing] = useState(false);
  const pending = useRef(false);

  function open(item: T): void {
    setTarget(item);
  }

  function close(): void {
    if (!pending.current) setTarget(null);
  }

  function submit(
    method: 'delete' | 'patch' | 'post' | 'put',
    url: string,
    data: Record<string, QueryValue | boolean> = {},
  ): void {
    if (pending.current) return;
    pending.current = true;
    setProcessing(true);
    router.visit(url, {
      method,
      data,
      preserveScroll: true,
      onNetworkError: () => {
        notify(CONNECTION_FAILURE);
        return false;
      },
      onHttpException: () => {
        notifyFailure(failureText);
        return false;
      },
      onSuccess: () => setTarget(null),
      onFinish: () => {
        pending.current = false;
        setProcessing(false);
      },
    });
  }

  return { target, processing, open, close, submit };
}
