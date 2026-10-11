import { Link } from '@inertiajs/react';
import PageScaffold from '@/components/UI/PageScaffold/PageScaffold';

const messages: Record<number, string> = {
  403: 'Acesso negado',
  404: 'Página não encontrada',
  419: 'Sessão expirada',
  500: 'Erro interno no servidor',
  503: 'Serviço indisponível',
};

export default function Error({ status }: { status: number }): React.JSX.Element {
  return (
    <PageScaffold title={`${status} — ${messages[status] ?? 'Não foi possível abrir esta página'}`}>
      <Link href="/">Voltar ao início</Link>
    </PageScaffold>
  );
}
