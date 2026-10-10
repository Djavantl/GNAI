# Bold React

Frontend em React e TypeScript. O projeto mantém somente a aplicação React, seus componentes, assets e ferramentas de desenvolvimento e build.

## Estrutura

- `react-app/src/`: aplicação, telas, componentes, hooks, utilitários, tipos e estilos.
- `react-app/public/`: imagens, fontes, modelos de e-mail e plugins utilizados pelos componentes.
- `react-app/docs/`: arquitetura e guias de componentes.
- `react-app/deploy/`: configuração do servidor estático de produção.
- `docker-compose.yml`: ambiente de desenvolvimento do React.

## Desenvolvimento com Docker

```bash
docker compose up --build
```

Aplicação: http://localhost:5173

## Desenvolvimento local

Use Node.js 22:

```bash
cd react-app
npm ci
npm run dev
```

## Validação e produção

```bash
cd react-app
npm run check
npm run build
npm run preview
```

O build é gerado em `react-app/dist/`. Consulte [os comandos de produção](react-app/README.md), [a arquitetura](react-app/docs/architecture.md) e [os guias de componentes](react-app/docs/README.md).
