# Aplicativos

As telas React usam os assets, fontes, ícones, cores e breakpoints do Bold. Os textos de interface e dos editores estão em pt-BR; nomes próprios, endereços e conteúdo demonstrativo em latim foram preservados.

## Telas e componentes

| Rota | Implementação | Interações preservadas |
| --- | --- | --- |
| `apps-inbox.html` | `Mailbox`, `MailboxSidebar`, `MailToolbar`, `MailList` | Pastas e marcadores, 20 mensagens, seleção individual, estados de leitura, anexos, hover, dropdowns e navegação para leitura/composição. |
| `apps-email-read.html` | `MailMessage` | Remetente e avatar originais, três anexos, editor de resposta em modo flutuante e barra de formatação ao selecionar texto. |
| `apps-email-compose.html` | `MailCompose` | Para/Cc/Cco/Assunto, Summernote de 350 px, formatação, diálogos e retorno à caixa de entrada. |
| `apps-calendar.html` | `CalendarPage`, `CalendarCategories`, `EventCalendar`, `CalendarDialogs` | Mês/semana/dia, navegação entre períodos, seleção de dias/horários, criação/edição/exclusão, arraste e redimensionamento do plugin, categorias arrastáveis e remoção após soltar. |
| `apps-contact.html` | `ContactDirectory`, `ContactCard`, `ContactForm`, `SocialLinks`, `Tooltip` | Oito contatos e avatares originais, ações exibidas no hover, tooltips, modal de adicionar com fade e campos rotulados. |

`Applications` apenas resolve e organiza as telas. Dados e configurações ficam em `*.data.ts`; contratos compartilhados ficam em `*.types.ts`. O estado e a integração do calendário ficam em `useEventCalendar`.

Foram reutilizados `Button`, `TextInput`, `ChoiceControl`, `CardBox`, `Panel`, `Table`, `ImageDisplay`, `Modal` e `SummernoteEditor`. `DropdownMenu`, `Tooltip` e `SocialLinks` têm pastas próprias e estilos locais. O teclado dos dropdowns permite abrir com as setas, percorrer opções e retornar ao botão com Escape.

## Estilos e comportamento

- Grid de 20 px entre colunas: margens de -10 px e paddings de 10 px, conforme `core.css`.
- E-mail: sidebar com 1/3 em 992–1199 px e 1/4 a partir de 1200 px; mensagem da tabela ocultada entre 768 e 1024 px; scroll horizontal interno e borda da tabela no celular.
- Botão de escrever/voltar vermelho, com contorno; toolbar azul; salvar/descartar rascunho verdes; enviar composição roxo; enviar resposta azul.
- Paginação da caixa de entrada com efeito de clique escuro; outros botões coloridos usam o efeito claro quando o legado especifica `waves-light`. `Button.wavesLight` permite escolher esse estado sem alterar o padrão dos consumidores existentes.
- Contatos: avatar de 80 px, conteúdo com recuo de 100 px, nome em maiúsculas, ações pequenas verde/vermelha, cinco links sociais de 30 px e modal de 600 px. Grade de duas colunas a partir de 768 px e três a partir de 1200 px.
- Modal de contato usa o efeito `fadein` existente, overlay original e cabeçalho `#36404a`. O formulário pode receber `onSave`; Cancelar fecha o modal localmente.
- Calendário usa o FullCalendar **2.3.2** e jQuery UI originais, com locale pt-BR. Mantém os slots de 15 minutos, faixa 08:00–19:00, altura inicial de janela menos 200 px, três eventos demonstrativos e suas cores.
- O modal de evento tem backdrop estático; categorias permitem fechar pelo fundo. O conteúdo permanece durante a animação de fechamento, sem trocar formulário ou título no meio do fade.
- O calendário reajusta a medida do plugin quando sua largura disponível muda. No legado, o primeiro cálculo no celular pode ocorrer antes do fechamento automático da sidebar, deixando linhas com altura desatualizada; esse estado temporário não foi reproduzido.

## Assets

Os runtimes de FullCalendar, jQuery UI e Summernote ficam em `public/assets/`. A aplicação React carrega seus arquivos locais e não depende de outra aplicação. O `.gitignore` permite versionar os diretórios `dist/` dos plugins, ignorando somente o build da aplicação.

## Limites da demonstração original

A aplicação não possui serviço de e-mail nem CRUD de contatos. Enviar, salvar rascunho, excluir e-mails, mudar páginas da caixa de entrada, aplicar ações de demonstração e pesquisar contatos continuam sem integração de domínio. O formulário de contato não persiste registros sem um consumidor fornecer `onSave`; editar/excluir cartões expõe callbacks opcionais. Não há mensagem falsa de envio ou de gravação.

Os eventos e categorias do calendário existem apenas durante a visita à tela, como no legado. Os três eventos iniciais usam as mesmas diferenças em milissegundos em relação ao horário atual.
