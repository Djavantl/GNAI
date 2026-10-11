# Desenvolvimento React

Esta pasta contém a base React/TypeScript do sistema Laravel.

- Preserve estilos, tokens, responsividade e comportamento dos componentes existentes.
- Use `components/{UI,Forms,Navigation,Tables}/PascalCase/PascalCase.tsx` e `pages/PascalCase/`.
- CSS, dados, hooks locais e tipos específicos ficam junto de seu proprietário.
- Imports entre pastas usam `@/`; imports locais usam `./`.
- Componentes não dependem de páginas ou app. Hooks compartilhados não dependem de UI.
- Utilitários e tipos não dependem das camadas superiores.
- Assets e plugins ficam em `public/assets/react`; preserve licenças e dependências transitivas.
- Não importe o CSS global do Bootstrap na entrada React.
- Mantenha a limpeza dos adaptadores de plugins ao desmontar componentes.
- Interface em pt-BR. Dados do catálogo são demonstrativos.
- Valide com `npm run check` e `npm run build` na raiz, ou pelo serviço Docker `node`.
- Verifique desktop, mobile e interações afetadas no catálogo `/react/index.html`.
- As telas Blade ainda estão ativas. Migre os fluxos conforme o escopo solicitado.
