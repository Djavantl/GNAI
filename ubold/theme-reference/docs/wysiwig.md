# Editor WYSIWYG

A página `/form-wysiwig.html` usa `TinyMceEditor`, que encapsula o TinyMCE 4.2.6 já presente no legado. Preserva o tema, os plugins, a área de edição de 300 px, a barra de ferramentas e os formatos personalizados. Menus, tooltips, diálogos, formatos e contador de palavras estão em pt-BR.

```tsx
import TinyMceEditor from '../components/TinyMceEditor/TinyMceEditor';

<TinyMceEditor
  defaultValue="<p>Conteúdo inicial.</p>"
  label="Descrição"
  height={300}
  onChange={(html) => setDescricao(html)}
/>
```

- `defaultValue`: HTML inicial confiável; para abrir outro documento, remonte usando uma `key` diferente.
- `onChange`: recebe HTML ao editar, formatar, desfazer ou refazer.
- `height`: altura inicial da área de edição.
- `label`: identificação acessível do editor.
- `toolbar`: permite configurar as ferramentas exibidas.
- `styleFormats`: formatos tipados; os exemplos da página ficam em `WysiwigPage.data.ts`.

Os estilos específicos ficam em `TinyMceEditor.css`, extraídos da seção Wysiwig de `assets/css/pages.css` do legado. A cor principal usa `--bold-color-custom`. O editor mantém seus estilos de conteúdo dentro do iframe e reutiliza os ícones e o skin originais.

Para reutilização em outro projeto, leve o componente, seu hook/configuração, `loadExternalScript` e a pasta `/assets/plugins/tinymce/`. Os assets são locais; não há dependência de CDN durante o uso nem necessidade de Bootstrap.

A tradução vem do [pacote TinyMCE i18n para a versão 4](https://github.com/mklkj/tinymce-i18n/blob/master/langs/pt_BR.js), com ajustes em “Subscrito”, “Endereço” e “Imagem de capa”. As licenças e atribuições acompanham os assets.

A configuração original contém um erro no token do botão de link; para manter a mesma barra, o link continua disponível pelo menu Inserir e pelo atalho Ctrl+K. A configuração padrão preserva isso; o consumidor pode incluir `link` em `toolbar`.

A página é demonstrativa, sem persistência no servidor. Assim como no legado, modelos e a correção ortográfica remota dependem de conteúdo/serviço fornecido pela aplicação; esta migração não adiciona um backend.
