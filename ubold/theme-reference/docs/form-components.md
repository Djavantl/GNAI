# Assistentes e campos formatados

As páginas `form-wizard.html` e `form-mask.html` são exemplos em português.
Seus dados ficam em `FormWizardPage.data.ts` e `FormMasks.data.ts`.
Os componentes abaixo não dependem dessas páginas.

## FormWizard

```tsx
import FormWizard from '../components/FormWizard/FormWizard';

<FormWizard
  id="cadastro"
  steps={[
    { id: 'conta', title: 'Conta', content: <DadosDaConta /> },
    { id: 'perfil', title: 'Perfil', content: <DadosDoPerfil /> },
    { id: 'confirmar', title: 'Confirmar', content: <Confirmacao /> }
  ]}
  orientation="horizontal"
  transition="slideLeft"
  onStepChanging={(atual, proxima) => validarEtapa(atual, proxima)}
  onFinish={() => salvar()}
/>
```

- `steps`: identificadores estáveis, títulos e conteúdo React. Os painéis permanecem montados para conservar valores.
- `orientation`: `horizontal` ou `vertical`.
- `transition`: `slideLeft` ou `fade`.
- `transitionDuration`: 200 ms por padrão; no fade são 200 ms de saída e 200 ms de entrada, como no legado.
- `onStepChanging`: retorne `false` para impedir a navegação. A mesma regra é consultada ao voltar.
- `onFinish`: retorne `false` para impedir a conclusão. O componente não envia dados nem abre alertas por conta própria.
- `labels`: permite substituir `previous`, `next`, `finish` e `navigation`.
- `className` e `style`: permitem personalização local. Variáveis: `--wizard-accent`, `--wizard-completed` e `--wizard-error`. O destaque usa `--bold-color-custom` por padrão.

Etapas ainda não visitadas ficam desabilitadas. As visitadas podem ser acessadas pelo título. As setas esquerda/direita navegam quando o foco não está em um campo.

A validação do exemplo fica em `useWizardValidation.ts`. Ela reproduz os campos obrigatórios e o e-mail do legado; não acrescenta regras de tamanho ou igualdade de senhas. A etapa de dicas contém os mesmos dados demonstrativos estáticos do original.

## MaskedInput e NumericInput

```tsx
import FormField from '../components/FormField/FormField';
import MaskedInput from '../components/MaskedInput/MaskedInput';
import NumericInput from '../components/NumericInput/NumericInput';

<FormField id="telefone" label="Telefone">
  <MaskedInput
    id="telefone"
    name="telefone"
    mask="(999) 999-9999"
    onValueChange={(formatado, bruto) => atualizarTelefone(bruto)}
  />
</FormField>

<NumericInput
  id="valor"
  name="valor"
  format={{ groupSeparator: '.', decimalSeparator: ',', currencySymbol: 'R$ ', min: 0, max: 9999.99 }}
  onValueChange={(formatado, bruto) => atualizarValor(bruto)}
/>
```

Os campos são não controlados: use `defaultValue` para o valor inicial e `onValueChange` para acompanhar o texto formatado e o valor bruto. Aceitam atributos nativos como `name`, `disabled`, `readOnly`, `aria-describedby`, `onFocus` e `onBlur`.

`MaskedInput` usa o padrão original: `9` representa um dígito, `a` uma letra e os demais caracteres são literais conforme o plugin. Mantém placeholders, edição de seleção, exclusão, Escape e limpeza de valores incompletos ao perder foco.

`NumericInput.format` aceita separadores, símbolo, posição `prefix`/`suffix`, agrupamento de 2/3/4 dígitos, limites `min`/`max` e `negativeBrackets`. O valor bruto é uma string numérica sem símbolo nem separadores de milhar.

Esses dois componentes encapsulam os plugins legados para preservar o comportamento. Ao reutilizá-los em outro projeto, leve `TextInput`, seus hooks, `loadExternalScript` e os assets:

- `/assets/js/jquery.min.js`
- `/assets/js/detect.js`
- `/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js`
- `/assets/plugins/autoNumeric/autoNumeric.js`

Não exigem Bootstrap CSS nem Bootstrap JS. Os eventos locais são removidos ao desmontar os campos.

## Estilos e composição

`TextInput` expõe `--input-border` e `--input-focus-border`. As fontes vêm de `--bold-font-body` e `--bold-font-ui`.

`FormField` reúne rótulo, campo, dica e mensagem de erro, com layout `stacked` ou `horizontal`. Passe ao campo `aria-describedby` apontando para `${id}-hint` ou `${id}-error` quando necessário.

`FormPage` e `FormSection` organizam título, breadcrumb e cards. A página de assistentes reutiliza também `ChoiceControl` nos termos de aceite.

As máscaras e moedas demonstrativas mantêm os formatos originais do legado. A tradução dos textos não converte automaticamente os exemplos para CPF, telefone brasileiro ou reais.
