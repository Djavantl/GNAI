# Summernote em React

A página `/form-summernote.html` demonstra o editor completo (350 px) e o modo inline, em pt-BR. O conteúdo demonstrativo está em `SummernotePage.data.ts`.

```tsx
import SummernoteEditor from '../components/SummernoteEditor/SummernoteEditor';

<SummernoteEditor
  defaultValue="<p>Escreva aqui.</p>"
  label="Descrição do produto"
  height={350}
  onChange={(html) => setDescricao(html)}
/>

<SummernoteEditor
  mode="inline"
  defaultValue="<p>Selecione este texto para formatar.</p>"
  onChange={(html) => setConteudo(html)}
/>
```

`defaultValue` é HTML inicial confiável, lido na montagem. O editor mantém o conteúdo durante a edição; `onChange` recebe o HTML atualizado. No modo código-fonte, a alteração é comunicada ao retornar à edição visual, como no plugin original. Para carregar outro documento, remonte o componente com uma `key` diferente.

`className` e `style` permitem personalização. A variável CSS `--summernote-accent` controla a cor dos botões, usando `--bold-color-custom` por padrão.

O componente encapsula o Summernote 0.6.16 do legado, reaproveitando o runtime jQuery e Bootstrap JS já existente no projeto. Os estilos necessários foram isolados no componente; não carrega Bootstrap CSS global. Para reutilizar em outro projeto, leve o componente, os helpers, `loadExternalScript`, as fontes/ícones do tema e estes assets:

- `/assets/js/jquery.min.js`
- `/assets/js/bootstrap.min.js`
- `/assets/plugins/summernote/summernote.min.js`
- `/assets/plugins/summernote/summernote-pt-BR.js`

A tradução original é complementada em `summernoteLocale.ts`, incluindo as operações de imagem. A página apenas compõe `FormPage`, `FormSection` e `SummernoteEditor`.

Menus, diálogos, barra flutuante e tooltips pertencem à instância do editor. Ao desmontar, são removidos os elementos auxiliares e os eventos de arrastar arquivos. O modo tela cheia mantém a geometria do legado e usa um listener próprio, preservando os listeners de redimensionamento dos outros componentes.

A inserção de imagens mantém o comportamento demonstrativo do legado: arquivos locais são incorporados ao HTML e URLs são referenciadas diretamente. O componente não persiste conteúdo em servidor.
