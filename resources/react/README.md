# React

Frontend React/TypeScript integrado ao Laravel com Inertia.

- Código: `resources/react` (alias `@/`).
- Assets: `public/assets/react`.
- Catálogo local: http://localhost/react/index.html.

Na raiz do projeto:

```bash
npm ci
npm run dev
npm run check
npm run build
```

O catálogo precisa do Laravel em execução. No ambiente Docker existente, use o
serviço `node` para os comandos npm. Não há servidor React separado.

Controllers de telas retornam `Inertia::render('Dominio/Recurso/Acao', $props)`.
O identificador resolve diretamente o arquivo `pages/Dominio/Recurso/Acao.tsx`.
As páginas de negócio são estruturas iniciais com título, prontas para receber
os formulários e demais interfaces. Os dados enviados pelos controllers ficam
disponíveis como props ou por `usePage()` do Inertia.

`main.tsx` inicializa o Inertia. A única entrada HTML é `resources/views/inertia.php`,
um documento PHP mínimo, sem layout Blade. O middleware compartilha usuário,
mensagens de sessão e erros de validação. Listagens usam respostas Inertia,
inclusive em requisições AJAX, sem fragmentos HTML de tabelas.

Os templates antigos das telas e seus arquivos em `resources/js` e `resources/css`
permanecem apenas como referência. Eles não são compilados pelo Vite nem chamados
pelos controllers de páginas. Templates para PDFs e e-mails continuam dedicados
a essas saídas. O Bootstrap instalado pelo npm foi removido; os plugins locais
utilizados pelos componentes React continuam em `public/assets/react`.

Reutilize os componentes com imports diretos, por exemplo:

```tsx
import Button from '@/components/UI/Button/Button';
import TextInput from '@/components/Forms/TextInput/TextInput';
```

A entrada React carrega reset, tokens e estilos globais próprios. Os componentes
carregam seus estilos e plugins locais. Somente as páginas do catálogo usam dados
demonstrativos. As páginas de negócio recebem os dados dos módulos pelos controllers.
