# React

Biblioteca de componentes, telas demonstrativas e assets integrada ao Laravel.

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

As telas de negócio permanecem em Blade enquanto são migradas por fluxo.
Reutilize os componentes com imports diretos, por exemplo:

```tsx
import Button from '@/components/UI/Button/Button';
import TextInput from '@/components/Forms/TextInput/TextInput';
```

A entrada React carrega reset, tokens e estilos globais próprios. Os componentes
carregam seus estilos e plugins locais. Os dados das páginas são exemplos e não
estão conectados aos módulos AEE ou Radar Inclusivo.
