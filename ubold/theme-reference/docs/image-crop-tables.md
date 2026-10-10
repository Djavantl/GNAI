# Recorte de imagem e tabelas

## Telas

- `/form-image-crop.html`: recorte, três prévias, coordenadas editáveis, zoom, rotação, ativação, restauração, substituição da imagem, proporção e exportação PNG/JPG.
- `/tables-basic.html`: exemplos básico, alternado, com bordas, hover, rolagem responsiva, compacto e contextual.
- `/tables-datatable.html`: busca, ordenação, tamanho de página e paginação.
- `/tables-editable.html`: inclusão/edição/exclusão de linhas com confirmação; edição de células por teclado, validação e totais.
- `/tables-responsive.html`: prioridades de colunas, menu de visibilidade, exibir tudo, foco e cabeçalho fixo.
- `/tables-foo-tables.html`: filtros, inclusão/exclusão, expansão, paginação e acordeão.
- `/tables-bootstrap.html`: seis exemplos com seleção múltipla/única, ordenação, formatação, busca, alternância de visualização e barra de ferramentas.

As alterações de dados permanecem em memória, como nas demonstrações legadas. Não foi criado serviço de persistência.

## Componentes

`ImageCropper` recebe `src`, `alt`, `aspectRatio`, `initialData`, `preview` e `onChange`. A referência expõe `command(method, argument)` para as operações do Cropper utilizadas pela tela. O seletor de prévia deve identificar apenas os elementos da instância atual. `getDataURL` retorna uma imagem real em base64; a aplicação consumidora decide como salvar ou enviar esse resultado.

```tsx
const cropper = useRef<ImageCropperHandle>(null);
<ImageCropper
  ref={cropper}
  src="/assets/images/big/img1.jpg"
  aspectRatio={16 / 9}
  onChange={setCropData}
/>
// Exportar:
const image = cropper.current?.command('getDataURL', 'image/jpeg');
```

`Table` fornece a base sem depender de página: `striped`, `bordered`, `hover`, `condensed` e atributos nativos de tabela. `appearance="inherit"` conserva o estilo de variantes existentes, como tabelas de painéis e dashboards.

```tsx
<Table striped hover>
  <thead><tr><th>Nome</th><th>Status</th></tr></thead>
  <tbody><tr><td>Marley</td><td>Ativo</td></tr></tbody>
</Table>
```

`TableDefinition`, em `Table/Table.types.ts`, descreve cabeçalhos, linhas e atributos de coluna. `TableContents` transforma esses dados em elementos React e reutiliza `TableLabel` para os indicadores de status. Os dados demonstrativos ficam em `TablesPage.data.ts`; as descrições ficam em `TablesPage.sections.data.ts`.

Os comportamentos são componentes separados:

| Componente | Propriedades principais |
| --- | --- |
| `SearchableTable` | `definition`, `pageSize` |
| `EditableTable` | `definition`, `onChange` |
| `InlineEditableTable` | `definition`, `maxTotal`, `onChange` |
| `ResponsiveTable` | `definition` |
| `FooTable` | `definition`, `mode` (`filter`, `rows`, `toggle`, `pagination`, `accordion`) |
| `BootstrapTable` | `definition`, `removable`, `onSelectionChange` |

A edição de células mantém a regra demonstrativa do legado: primeira coluna obrigatória, demais colunas numéricas e total da segunda coluna até 5000. `maxTotal` permite ajustar esse limite.

## Reutilização nas telas existentes

As tabelas de pedidos recentes (`DataTable`), projetos do Dashboard 3, painéis, demonstrações de modais, SweetAlert e grid passaram a utilizar `Table`. Seus estilos próprios foram preservados; não se aplicou a aparência padrão sobre variantes visualmente distintas.

Os três seletores de horário de Form Pickers agora usam `TimePicker`, com props `id`, `label`, `showMeridian`, `minuteStep`, `defaultValue` e `onChange`. O componente possui estilos e ciclo de vida próprios, sem depender do inicializador da página.

```tsx
<TimePicker label="Horário" showMeridian={false} minuteStep={15}
  defaultValue="09:30" onChange={setTime} />
```

Não foram encontrados outros campos nativos `type="time"` ou `type="date"` nas telas React para substituir automaticamente.

## Integração e fidelidade

Os motores locais usados pelas páginas legadas foram mantidos em adaptadores React para preservar os comportamentos. Não há iframe nem importação de página HTML legada. `PluginTable` renderiza a estrutura dos componentes e entrega apenas sua subárvore ao plugin; desmontagem remove a instância, DOM e os handlers globais instalados por ela. Textos digitados na edição de linhas são escapados antes de chegar ao motor da tabela.

O Cropper carregado pela página original é a versão **0.7.0** em `cropper.min.js`; o arquivo não minificado do legado é outra versão e não foi utilizado. As folhas de compatibilidade das tabelas estão limitadas a `.tableSurface`, sem instalar Bootstrap ou importar seu CSS global.

## Verificações realizadas

- Comparação de estilos e dimensões das seis telas com o legado em 1440 px; revisão responsiva em 375, 768 e 1024 px.
- Recorte: controles, coordenadas após arraste, rotação, zoom, substituição, prévias e exportação PNG/JPG.
- Tabelas: hover, busca, ordenação numérica, paginação, seleção, edição/salvamento/cancelamento, exclusão confirmada, validação numérica e atualização de totais.
- FooTable: expansão no celular, acordeão, busca e inclusão/exclusão de linhas.
- Tabela responsiva: foco, visibilidade de colunas e exibir tudo.
- Ciclos de navegação entre tabelas, recorte e uploads sem acúmulo de handlers globais ou elementos de plugins.
- Build Vite e verificação TypeScript estrita dos novos componentes.

A tradução dos cabeçalhos e dados altera naturalmente algumas larguras de coluna e quebras de texto. Os breakpoints, espaçamentos, estilos e motores de interação seguem o legado.
