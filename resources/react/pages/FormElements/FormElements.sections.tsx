import Button from '@/components/UI/Button/Button';
import {
  inputSizeFields,
  inputStateFields,
  inputTypeLeftFields,
  inputTypeRightFields
} from './FormElements.data';
import {
  Description,
  DropdownButton,
  FormCard,
  FormField,
  LegacyCheckbox,
  PreventSubmitForm,
  SectionTitle
} from './FormElements.controls';

export function FormElementsPageTitle(): React.JSX.Element {
  return (
    <div className="formElementsPage__row formElementsPage__pageTitleRow">
      <div className="formElementsPage__col col-sm-12">
        <h4 className="page-title">Elementos de formulário</h4>
        <ol className="breadcrumb">
          <li><a href="#" onClick={(event) => event.preventDefault()}>Interface</a></li>{' '}
          <li>{' '}<a href="#" onClick={(event) => event.preventDefault()}>Formulários</a></li>{' '}
          <li className="active">{' '}Elementos gerais</li>
        </ol>
      </div>
    </div>
  );
}

export function InputTypesCard(): React.JSX.Element {
  return (
    <FormCard>
      <SectionTitle>Tipos de campo</SectionTitle>
      <Description>
        Controle de formulário mais comum, com campos de texto. Inclui suporte para todos os tipos HTML5: <code>text</code>, <code>password</code>, <code>datetime</code>, <code>datetime-local</code>, <code>date</code>, <code>month</code>, <code>time</code>, <code>week</code>, <code>number</code>, <code>email</code>, <code>url</code>, <code>search</code>, <code>tel</code> e <code>color</code>.
      </Description>
      <div className="formElementsPage__row">
        <div className="formElementsPage__col col-md-6">
          <PreventSubmitForm className="form-horizontal">
            {inputTypeLeftFields.map((field, index) => <FormField field={field} key={`${field.label}-${index}`} />)}
          </PreventSubmitForm>
        </div>
        <div className="formElementsPage__col col-md-6">
          <PreventSubmitForm className="form-horizontal">
            {inputTypeRightFields.map((field, index) => <FormField field={field} key={`${field.label}-${index}`} />)}
          </PreventSubmitForm>
        </div>
      </div>
    </FormCard>
  );
}

export function InputStatesAndSizesCard(): React.JSX.Element {
  return (
    <FormCard>
      <div className="formElementsPage__row">
        <div className="formElementsPage__col col-md-6">
          <SectionTitle>Estados dos campos</SectionTitle>
          <Description>O Bootstrap inclui estilos de validação para estados de erro, aviso e sucesso nos controles de formulário.</Description>
          <PreventSubmitForm className="form-horizontal">
            {inputStateFields.map((field, index) => <FormField field={field} key={`${field.label}-${index}`} />)}
          </PreventSubmitForm>
        </div>
        <div className="formElementsPage__col col-md-6">
          <SectionTitle>Tamanhos dos campos</SectionTitle>
          <Description>
            Defina alturas usando classes como <code>.input-lg</code> e defina larguras usando classes de coluna da grid, como <code>.col-lg-*</code>.
          </Description>
          <PreventSubmitForm className="form-horizontal">
            {inputSizeFields.map((field, index) => <FormField field={field} key={`${field.label ?? field.placeholder}-${index}`} />)}
          </PreventSubmitForm>
        </div>
      </div>
    </FormCard>
  );
}

export function InputGroupsCard(): React.JSX.Element {
  return (
    <FormCard>
      <SectionTitle>Grupos de campos</SectionTitle>
      <Description>
        Estenda controles de formulário adicionando texto ou botões antes, depois ou nos dois lados de qualquer <code>&lt;input&gt;</code> de texto. Use <code>.input-group</code> com <code>.input-group-addon</code> ou <code>.input-group-btn</code> para adicionar elementos antes ou depois de um único <code>.form-control</code>.
      </Description>
      <div className="formElementsPage__row">
        <div className="formElementsPage__col col-md-4">
          <PreventSubmitForm>
            <div className="form-group">
              <label className="control-label" htmlFor="example-input1-group1">Estático</label>
              <div className="input-group">
                <span className="input-group-addon"><i className="fa fa-user" /></span>
                <input type="text" id="example-input1-group1" name="example-input1-group1" className="form-control" placeholder="Usuário" />
              </div>
              <div className="input-group m-t-10">
                <input type="email" id="example-input2-group1" name="example-input2-group1" className="form-control" placeholder="Email" />
                <span className="input-group-addon"><i className="fa fa-envelope-o" /></span>
              </div>
              <div className="input-group m-t-10">
                <span className="input-group-addon"><i className="fa fa-dollar" /></span>
                <input type="text" id="example-input3-group1" name="example-input3-group1" className="form-control" placeholder=".." />
                <span className="input-group-addon">.00</span>
              </div>
            </div>
          </PreventSubmitForm>
        </div>
        <div className="formElementsPage__col col-md-4">
          <PreventSubmitForm>
            <div className="form-group">
              <label className="control-label" htmlFor="example-input1-group2">Botões</label>
              <div className="input-group">
                <span className="input-group-btn"><Button tone="primary" type="button" iconOnly aria-label="Pesquisar" leftIconClass="fa fa-search" /></span>
                <input type="text" id="example-input1-group2" name="example-input1-group2" className="form-control" placeholder="Pesquisar" />
              </div>
              <div className="input-group m-t-10">
                <input type="email" id="example-input2-group2" name="example-input2-group2" className="form-control" placeholder="Email" />
                <span className="input-group-btn"><Button tone="primary" type="button" label="Enviar" /></span>
              </div>
              <div className="input-group m-t-10">
                <span className="input-group-btn"><Button tone="primary" type="button" iconOnly aria-label="Facebook" leftIconClass="fa fa-facebook" /></span>
                <input type="text" id="example-input3-group2" name="example-input3-group2" className="form-control" placeholder="Pesquisar" />
                <span className="input-group-btn"><Button tone="primary" type="button" iconOnly aria-label="Twitter" leftIconClass="fa fa-twitter" /></span>
              </div>
            </div>
          </PreventSubmitForm>
        </div>
        <div className="formElementsPage__col col-md-4">
          <PreventSubmitForm>
            <div className="form-group">
              <label className="control-label" htmlFor="example-input1-group3">Menus suspensos</label>
              <div className="input-group">
                <DropdownButton />
                <input type="text" id="example-input1-group3" name="example-input1-group3" className="form-control" placeholder="Usuário" />
              </div>
              <div className="input-group m-t-10">
                <input type="email" id="example-input2-group3" name="example-input2-group3" className="form-control" placeholder="Email" />
                <DropdownButton alignRight />
              </div>
              <div className="input-group m-t-10">
                <DropdownButton split />
                <input type="text" id="example-input3-group3" name="example-input3-group3" className="form-control" placeholder=".." />
                <DropdownButton label="" alignRight />
              </div>
            </div>
          </PreventSubmitForm>
        </div>
      </div>
    </FormCard>
  );
}

export function FormExamplesCard(): React.JSX.Element {
  return (
    <FormCard>
      <div className="formElementsPage__row">
        <div className="formElementsPage__col col-md-6">
          <SectionTitle>Exemplo básico</SectionTitle>
          <Description>
            Controles individuais de formulário recebem automaticamente alguns estilos globais. Todos os elementos textuais <code>&lt;input&gt;</code>, <code>&lt;textarea&gt;</code> e <code>&lt;select&gt;</code> com <code>.form-control</code> usam <code>width: 100%;</code> por padrão. Envolva labels e controles com <code>.form-group</code> para obter o espaçamento ideal.
          </Description>
          <PreventSubmitForm>
            <div className="form-group">
              <label htmlFor="exampleInputEmail1">Endereço de email</label>
              <input type="email" className="form-control" id="exampleInputEmail1" placeholder="Digite o email" />
            </div>
            <div className="form-group">
              <label htmlFor="exampleInputPassword1">Senha</label>
              <input type="password" className="form-control" id="exampleInputPassword1" placeholder="Senha" />
            </div>
            <div className="form-group"><LegacyCheckbox id="checkbox1" label="Lembrar de mim" /></div>
            <Button tone="purple" type="submit" label="Enviar" />
          </PreventSubmitForm>
        </div>
        <div className="formElementsPage__col col-md-6">
          <SectionTitle>Formulário horizontal</SectionTitle>
          <Description>
            Use as classes de grid predefinidas do Bootstrap para alinhar labels e grupos de controles em um layout horizontal adicionando <code>.form-horizontal</code> ao formulário, que não precisa necessariamente ser um <code>&lt;form&gt;</code>. Isso faz com que <code>.form-group</code>s se comportem como linhas de grid, sem precisar de <code>.row</code>.
          </Description>
          <PreventSubmitForm className="form-horizontal">
            <FormField field={{ label: 'Email', labelFor: 'inputEmail3', labelClassName: 'col-sm-3', controlClassName: 'col-sm-9', id: 'inputEmail3', type: 'email', placeholder: 'Email' }} />
            <FormField field={{ label: 'Senha', labelFor: 'inputPassword3', labelClassName: 'col-sm-3', controlClassName: 'col-sm-9', id: 'inputPassword3', type: 'password', placeholder: 'Senha' }} />
            <FormField field={{ label: 'Repetir senha', labelFor: 'inputPassword4', labelClassName: 'col-sm-3', controlClassName: 'col-sm-9', id: 'inputPassword4', type: 'password', placeholder: 'Digite a senha novamente' }} />
            <div className="form-group">
              <div className="col-sm-offset-3 col-sm-9"><LegacyCheckbox id="checkbox2" label="Marque aqui!" /></div>
            </div>
            <div className="form-group m-b-0">
              <div className="col-sm-offset-3 col-sm-9"><Button tone="info" type="submit" label="Entrar" /></div>
            </div>
          </PreventSubmitForm>
        </div>
      </div>
    </FormCard>
  );
}

export function InlineFormCard(): React.JSX.Element {
  return (
    <FormCard>
      <SectionTitle>Formulário inline</SectionTitle>
      <Description>
        Adicione <code>.form-inline</code> ao formulário, que não precisa necessariamente ser um <code>&lt;form&gt;</code>, para controles alinhados à esquerda e em bloco inline. <strong>Isso se aplica apenas a formulários em viewports com pelo menos 768px de largura.</strong>
      </Description>
      <div className="formElementsPage__row m-b-30">
        <div className="formElementsPage__col col-sm-12">
          <h5><b>Estilo 1</b></h5>
          <PreventSubmitForm className="form-inline">
            <div className="form-group">
              <label className="sr-only" htmlFor="exampleInputEmail2">Endereço de email</label>
              <input type="email" className="form-control" id="exampleInputEmail2" placeholder="Digite o email" />
            </div>
            {' '}
            <div className="form-group m-l-10">
              <label className="sr-only" htmlFor="exampleInputPassword2">Senha</label>
              <input type="password" className="form-control" id="exampleInputPassword2" placeholder="Senha" />
            </div>
            {' '}
            <div className="form-group m-l-10"><LegacyCheckbox id="checkbox3" label="Lembrar de mim" /></div>
            {' '}
            <Button tone="success" type="submit" size="md" className="m-l-10" label="Entrar" />
          </PreventSubmitForm>
        </div>
      </div>
      <div className="formElementsPage__row">
        <div className="formElementsPage__col col-sm-12">
          <h5><b>Estilo 2</b></h5>
          <PreventSubmitForm className="form-inline">
            <div className="form-group m-r-10">
              <label htmlFor="exampleInputName2">Nome</label>{' '}
              <input type="text" className="form-control" id="exampleInputName2" placeholder="Joana Silva" />
            </div>
            {' '}
            <div className="form-group m-r-10">
              <label htmlFor="exampleInputEmailInline2">Email</label>{' '}
              <input type="email" className="form-control" id="exampleInputEmailInline2" placeholder="joana.silva@exemplo.com" />
            </div>
            {' '}
            <Button tone="default" type="submit" size="md" label="Enviar convite" />
          </PreventSubmitForm>
        </div>
      </div>
    </FormCard>
  );
}
