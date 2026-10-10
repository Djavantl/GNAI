# Pages

As 14 rotas do menu Pages agora usam componentes React. Assets, cores e
dimensões mantêm a identidade visual do Bold. Os textos da
interface estão em pt-BR, conforme solicitado, incluindo campos, ações,
mensagens de erro, breadcrumb e rótulos acessíveis. Nomes próprios,
marcas e endereços das rotas foram preservados.

`Pages` orquestra a tela a partir de `Pages.data.ts`. Login, cadastro,
recuperação, bloqueio e erros usam `AccountLayout`, sem a navegação do
dashboard, como no legado. Starter e 404-alt continuam dentro do layout
principal, com o item correspondente ativo no menu lateral.

## Componentes compartilhados

- `AccountLayout`: fundo original `agsquare.png`, wrapper de 420 px e
  variante larga com os breakpoints originais.
- `AccountCard`: título e corpo do card de conta.
- `CredentialsForm`: campos de login/cadastro, checkbox e botão de envio.
  Aceita `mode`, `compact`, `social`, `action`, `copy` e `onSubmit`.
- `SocialLoginButtons`: botões sociais originais, com callback opcional
  `onSelect`. As páginas demonstrativas mantêm os botões sem integração
  externa, como no legado.
- `InputAction`: campo e botão de envio agrupados, usado na recuperação
  de senha e no bloqueio.
- `ErrorState`: código com os ícones originais, título, texto e link de
  retorno. Aceita `code`, `description` e `homeHref`.
- `InlineAlert`: alerta extraído da tela UI Bootstrap e reutilizado na
  recuperação de senha. Conserva variantes, fechamento e fade.

Botões, inputs, checkboxes, imagens, breadcrumb e loader existentes são
reutilizados. `ChoiceControl.label` agora aceita conteúdo React para o
link dos termos dentro do rótulo. Os estilos específicos ficam junto dos
componentes; dados e tipos estão em arquivos `.data.ts` e `.types.ts`.

## Comportamento

Os formulários mantêm a validação nativa, os estados iniciais dos checkboxes,
os links, os destinos e os métodos de envio do HTML original. Os formulários
de recuperação e bloqueio continuam usando POST. São exemplos de frontend:
não foi acrescentado um serviço de autenticação ou de envio de e-mail.

O layout combinado empilha login e cadastro abaixo de 1200 px. Os wrappers
usam 90% da largura abaixo de 768 px. O loader e o efeito Waves existentes
também funcionam ao abrir diretamente uma página independente.
