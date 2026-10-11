# Desenvolvimento React

Esta pasta contém a base React/TypeScript do sistema Laravel.

- Preserve estilos, tokens, responsividade e comportamento dos componentes existentes, observando a identidade Gnai e os limites documentados no `AGENTS.md` da raiz; aparência demonstrativa não define a marca do produto.
- Use `components/{UI,Forms,Navigation,Tables}/PascalCase/PascalCase.tsx` e `pages/PascalCase/`.
- CSS, dados, hooks locais e tipos específicos ficam junto de seu proprietário.
- Imports entre pastas usam `@/`; imports locais usam `./`.
- Componentes não dependem de páginas ou app. Hooks compartilhados não dependem de UI.
- Utilitários e tipos não dependem das camadas superiores.
- Assets e plugins do tema ficam em `public/assets/react`; assets próprios do Gnai ficam em `public/images/`. Consulte o inventário da raiz antes de escolher logo/banner/imagens; preserve licenças e dependências transitivas.
- Não importe o CSS global do Bootstrap na entrada React.
- Mantenha a limpeza dos adaptadores de plugins ao desmontar componentes.
- Interface em pt-BR. Dados do catálogo são demonstrativos.
- Valide com `npm run check` e `npm run build` na raiz, ou pelo serviço Docker `node`.
- Verifique desktop, mobile e interações afetadas no catálogo `/react/index.html`.
- Controllers de telas retornam Inertia e páginas em `resources/react/pages`; não crie respostas HTML parciais.
- Páginas Inertia usam `pages/Dominio/Recurso/Acao.tsx` e o identificador `Dominio/Recurso/Acao`.
- Os templates antigos de telas ficam preservados apenas como referência; não são uma alternativa de renderização.
