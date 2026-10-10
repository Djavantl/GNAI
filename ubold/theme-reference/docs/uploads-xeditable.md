# Upload e edição inline

Páginas migradas:

- `/form-uploads.html`: upload múltiplo em pt-BR.
- `/form-xeditable.html`: seis exemplos, nos modos inline e popover, em pt-BR.

## FileDropzone

```tsx
import FileDropzone from '../components/FileDropzone/FileDropzone';

<FileDropzone
  uploadUrl="/api/arquivos"
  maxFileSizeMb={20}
  acceptedFiles="image/*,.pdf"
  onFilesChange={(files) => acompanharSelecao(files)}
  onSuccess={(file, response) => registrarEnvio(file, response)}
  onError={(file, message) => mostrarErro(file, message)}
/>
```

Encapsula o Dropzone original: seleção múltipla, arrastar e soltar, miniaturas, nome/tamanho, progresso, sucesso e erro. O envio usa POST multipart/form-data, campo `file`, para `uploadUrl`. `maxFileSizeMb` mantém o padrão legado de 256; `acceptedFiles` é opcional. Os estilos específicos ficam junto do componente, e a página reutiliza `Button`.

O envio é automático quando existe `uploadUrl`. A referência `FileDropzoneHandle` também expõe `submit()` para processar a fila. A desmontagem cancela uploads pendentes e remove o input auxiliar.

A página legada usa `action="#"`, que retorna erro 405 no servidor estático, e seu botão Submit não tem handler. A página React permanece demonstrativa, sem endpoint: arquivos selecionados exibem a mensagem “Nenhum destino de envio foi configurado.”, sem simular sucesso ou enviar arquivos à rota da página. Para envio real, forneça `uploadUrl`; nenhum backend foi criado nesta migração.

## EditableField

```tsx
import EditableField from '../components/EditableField/EditableField';

<EditableField
  id="nome"
  title="Digite seu nome"
  mode="inline"
  defaultValue="Marley"
  required
  onChange={(value) => atualizarNome(value)}
/>

<EditableField
  id="situacao"
  title="Selecione a situação"
  mode="popup"
  type="select"
  defaultValue={1}
  options={[
    { value: 1, text: 'Ativo', color: '#5fbeaa' },
    { value: 2, text: 'Inativo', color: '#5d9cec' }
  ]}
  onChange={(value) => atualizarSituacao(value)}
/>
```

- `type`: `text`, `select`, `textarea` ou `combodate`.
- `mode`: `inline` ou `popup`.
- `defaultValue` e `initialText`: valor inicial e, opcionalmente, texto inicial independente da fonte remota.
- `options`: opções locais, com cor de exibição opcional.
- `sourceUrl`: URL de leitura das opções remotas; o exemplo `/status` preserva a demonstração de falha do legado.
- `required`, `placeholder`, `title` e `placement`: apresentação e validação.
- `onChange`: valor salvo localmente, como string ou número; datas são normalizadas para `YYYY-MM-DD` e exibidas como `DD/MM/YYYY`.

Salvar e cancelar preservam o comportamento do X-Editable original. Escape cancela e Ctrl+Enter salva comentários. Datas mantêm o intervalo de anos original (1970–2015) e usam meses em português sem mudar o locale global do Moment. Os dados da página ficam em `XEditablePage.data.ts`.

A edição é local (`send: 'never'`), como nos exemplos legados sem URL de gravação. Use `onChange` para integrar a persistência da sua aplicação.

## Assets

Para reutilização, leve os componentes, seus hooks, `loadExternalScript` e os assets usados:

- `/assets/plugins/dropzone/dropzone.js`
- `/assets/js/jquery.min.js`
- `/assets/js/bootstrap.min.js` (runtime já existente no projeto)
- `/assets/plugins/moment/moment.js`
- `/assets/plugins/x-editable/js/bootstrap-editable.min.js`
- `/assets/plugins/x-editable/img/`

O X-Editable reutiliza os ícones `md-done`/`md-clear` e as fontes do tema. Seus estilos de formulário/popover são locais ao componente; não é carregado Bootstrap CSS global.
