# Páginas React de visualização e detalhes

Esta convenção reúne o padrão compartilhado das telas de visualização do Gnai. As visualizações de materiais pedagógicos e tecnologias assistivas são as referências para detalhes diretos; o prontuário do aluno é a referência para registros extensos organizados em resumo e abas.

## Estrutura comum

- Use a estrutura de página de negócio já integrada, com `Navigation/PageTitle` e breadcrumb fornecidos pelo wrapper do domínio.
- Reutilize `UI/CardBox` e `Forms/FormSection variant="inline"` para organizar os dados. Títulos e ícones de seção usam o cinza compartilhado; labels usam `--ui-color-form-label` e valores usam `--ui-color-legacy-text`.
- Apresente pares de label e valor com HTML semântico, preferencialmente `dl`, `dt` e `dd`. Textos ricos vindos do servidor só podem ser renderizados pelo mesmo contrato sanitizado já adotado pela funcionalidade.
- Status e classificações curtas usam `UI/TableLabel`; ações usam `UI/Button`. Não recrie badges, cards ou estilos de botão dentro da página.
- Mantenha `Forms/FormActions` imediatamente depois do conteúdo principal. Preserve ações, permissões, links, downloads, modais e nomes acessíveis existentes.

## Visualizações simples

Telas com poucos grupos de informação devem permanecer em um card com seções inline. Não adicione abas quando a leitura sequencial for curta e clara. Materiais pedagógicos e tecnologias assistivas demonstram a composição esperada de labels, valores, badges e ações.

## Prontuários e visualizações extensas

Quando uma visualização reúne muitos grupos independentes e provoca rolagem excessiva:

1. Mostre primeiro um card compacto de identificação, contendo apenas os dados essenciais já existentes no contrato, como foto, nome, código ou matrícula, status e classificação principal.
2. Organize os demais grupos com `Navigation/Tabs`, usando a variante vertical em telas largas. Cada aba representa uma seção funcional completa, não um campo isolado.
3. A navegação lateral permanece neutra; somente o texto da aba ativa e seus estados de interação usam `--ui-color-gnai-primary`. Não aplique fundo roxo à aba ativa.
4. Em telas estreitas, transforme a navegação em uma faixa horizontal rolável acima do conteúdo. O painel continua ocupando a largura disponível.
5. Monte somente as abas permitidas ao usuário. A ocultação visual acompanha as permissões recebidas, sem substituir a autorização do servidor.
6. Quando a aba representa uma coleção própria do registro, apresente sua listagem completa dentro da aba com os componentes de `Tables/TableList`. Não mantenha um resumo limitado acompanhado por botão “Gerenciar” se a aba já comporta a tabela integral.
7. A tabela embutida preserva primeira e última colunas fixas, seletor de colunas, redimensionamento apenas das colunas intermediárias, estado vazio, contagem e ações compactas conforme a convenção de listagens. Como todos os registros já pertencem à aba, a contagem estática substitui paginação artificial.
8. Quando autorizado, o cadastro fica acima da tabela com `TableListCreateButton`. Visualização, edição, download e exclusão permanecem nas ações da linha conforme os contratos e permissões da funcionalidade; exclusão usa `ConfirmActionModal`.
9. Fluxos abertos a partir de uma aba devem retornar ao prontuário com o parâmetro `tab` correspondente. Botões Voltar/Cancelar e redirects de sucesso ou exclusão preservam essa origem, em vez de encaminhar para uma segunda tela de listagem.

## Acessibilidade das abas

- O componente compartilhado deve expor `tablist`, `tab` e `tabpanel`, relacionando controles e painéis com IDs, `aria-controls` e `aria-labelledby`.
- Use foco roving: apenas a aba selecionada participa da ordem natural. Setas navegam conforme a orientação, e `Home`/`End` levam à primeira/última aba.
- Labels visíveis devem ser compreensíveis. Use `accessibleLabel` apenas quando a forma abreviada exibida não explicar suficientemente o destino.
- Foco precisa permanecer visível tanto nas abas quanto no painel. Mudanças de estado não podem ser comunicadas somente pela cor.

## Responsividade e escopo

- Use grades fluidas com `minmax(0, 1fr)` e permita quebra de textos longos. Tabelas dentro das abas devem manter uma região com rolagem horizontal quando necessário.
- A versão compacta não remove dados, ações ou regras do legado. Ela apenas reorganiza a leitura.
- Não transforme excepcionalidades de um domínio em estilo global. CSS local pode compor o resumo e o contêiner das abas; tokens, botões, seções, badges e navegação continuam compartilhados.
