import { maxLengthLeftInputs, maxLengthRightInputs } from './FormAdvanced.content';
import { useState, type ReactNode } from 'react';
import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  bootstrapSelectColorDemos,
  bootstrapSelectCountOptions,
  bootstrapSelectGroups,
  bootstrapSelectIconDemo,
  bootstrapSelectOptions,
  bootstrapSelectSearchOptions,
  bootstrapSelectTitledOptions,
  countryOptions,
  fileStyleLeftDemos,
  fileStyleRightDemos,
  groupedTeamOptions,
  switcheryBasicItems,
  switcherySecondaryItems,
  switcherySizeItems,
  tagCities,
  teamOptions,
  timeZoneGroups,
  touchSpinLeftInputs,
  touchSpinRightInputs
} from './FormAdvanced.data';
import type {
  BootstrapSelectDemo,
  FileStyleDemo,
  MaxLengthInputConfig,
  SelectOption,
  SelectOptionGroup,
  SwitcheryItem,
  TouchSpinInputConfig
} from './FormAdvanced.types';

function Row({ children }: { children: ReactNode }): JSX.Element {
  return <div className="formAdvancedPage__row row">{children}</div>;
}

function Col({ children, md = 12, lg }: { children: ReactNode; md?: 4 | 6 | 12; lg?: 4 }): JSX.Element {
  return <div className={`formAdvancedPage__col col-md-${md}${lg ? ` col-lg-${lg}` : ''}`}>{children}</div>;
}

function SectionTitle({ children, className = 'm-t-0 header-title' }: { children: ReactNode; className?: string }): JSX.Element {
  return <h4 className={className}><b>{children}</b></h4>;
}

function SmallTitle({ children, className }: { children: ReactNode; className?: string }): JSX.Element {
  return <h5 className={className}><b>{children}</b></h5>;
}

function Description({ children, className = 'text-muted m-b-30 font-13' }: { children: ReactNode; className?: string }): JSX.Element {
  return <p className={className}>{children}</p>;
}

function PreventSubmitForm({ children }: { children: ReactNode }): JSX.Element {
  return (
    <form
      onSubmit={(event) => {
        event.preventDefault();
      }}
    >
      {children}
    </form>
  );
}

function renderOptions(options: SelectOption[]): JSX.Element[] {
  return options.map((option: SelectOption) => (
    <option value={option.value} key={option.value}>
      {option.label}
    </option>
  ));
}

function renderOptionGroups(groups: SelectOptionGroup[]): JSX.Element[] {
  return groups.map((group: SelectOptionGroup) => (
    <optgroup label={group.label} key={group.label}>
      {renderOptions(group.options)}
    </optgroup>
  ));
}

function SwitcheryInput({ item }: { item: SwitcheryItem }): JSX.Element {
  return (
    <input
      type="checkbox"
      defaultChecked={item.checked}
      data-plugin="switchery"
      data-color={item.color}
      data-secondary-color={item.secondaryColor}
      data-size={item.size}
    />
  );
}

function BootstrapSelect({ demo }: { demo: BootstrapSelectDemo }): JSX.Element {
  return (
    <select
      className={`selectpicker${demo.showTick ? ' show-tick' : ''}${demo.className ? ` ${demo.className}` : ''}`}
      data-style={demo.dataStyle}
      data-live-search={demo.liveSearch ? 'true' : undefined}
      data-max-options={demo.maxOptions}
      data-selected-text-format={demo.selectedTextFormat}
      disabled={demo.disabled}
      multiple={demo.multiple}
    >
      {demo.optgroups
        ? demo.optgroups.map((group) => (
          <optgroup label={group.label} key={group.label}>
            {renderOptions(group.options)}
          </optgroup>
        ))
        : demo.options.map((option) => (
          <option value={option.value} data-icon={option.icon} title={option.title} key={option.value}>
            {option.label}
          </option>
        ))}
    </select>
  );
}

function FileStyleInput({ demo, index }: { demo: FileStyleDemo; index: number }): JSX.Element {
  return (
    <div className={`form-group${index === 4 ? ' m-b-0' : ''}`}>
      <label className="control-label">{demo.label}</label>
      <input type="file" className="filestyle" {...demo.attributes} />
    </div>
  );
}

function TouchSpinInput({ input, isLast = false }: { input: TouchSpinInputConfig; isLast?: boolean }): JSX.Element {
  return (
    <div className={`form-group${isLast ? ' m-b-0' : ''}`}>
      <label className="control-label">{input.label}</label>
      <input
        id={input.id}
        type="text"
        defaultValue={input.value ?? ''}
        name={input.name}
        className={input.className}
        {...input.attributes}
      />
    </div>
  );
}

function TouchSpinButtonGroupInput({ input }: { input: TouchSpinInputConfig }): JSX.Element {
  const [open, setOpen] = useState<boolean>(false);

  return (
    <div className="form-group m-b-0">
      <label className="control-label">{input.label}</label>
      <div className="input-group">
        <input id={input.id} type="text" className="form-control" name={input.name} defaultValue={input.value ?? ''} />
        <div className={`input-group-btn${open ? ' open' : ''}`}>
          <button type="button" className="btn btn-default dropdown-toggle" onClick={() => setOpen((currentValue) => !currentValue)}>
            <span className="caret" />
            <span className="sr-only">Alternar dropdown</span>
          </button>
          {open ? (
            <ul className="dropdown-menu pull-right" role="menu">
              <li><a href="#" onClick={(event) => event.preventDefault()}>Ação</a></li>
              <li><a href="#" onClick={(event) => event.preventDefault()}>Outra ação</a></li>
              <li><a href="#" onClick={(event) => event.preventDefault()}>Algo mais aqui</a></li>
              <li className="divider" />
              <li><a href="#" onClick={(event) => event.preventDefault()}>Link separado</a></li>
            </ul>
          ) : null}
        </div>
      </div>
    </div>
  );
}

function MaxLengthInput({ input }: { input: MaxLengthInputConfig }): JSX.Element {
  return (
    <div className={input.id === 'defaultconfig' || input.id === 'alloptions' ? undefined : 'm-t-20'}>
      <SmallTitle>{input.label}</SmallTitle>
      <Description className="text-muted m-b-15 font-13">{input.description}</Description>
      {input.multiline ? (
        <textarea id={input.id} className="form-control" maxLength={225} rows={2} placeholder={input.placeholder} />
      ) : (
        <input type="text" className="form-control" maxLength={25} name={input.id} id={input.id} />
      )}
    </div>
  );
}

export function FormAdvancedPageTitle(): JSX.Element {
  return (
    <div className="formAdvancedPage__pageTitleRow">
      <div className="formAdvancedPage__col formAdvancedPage__col--full">
        <h4 className="page-title formAdvancedPage__pageTitle">Plugins avançados</h4>
        <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'Formulários', href: '#' }, { label: 'Plugins avançados' }]} />
      </div>
    </div>
  );
}

export function TagsInputSection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle className="m-t-0 m-b-20 header-title">Tags Input</SectionTitle>
          <Row>
            <Col md={6}>
              <SmallTitle>Input com tags</SmallTitle>
              <Description>
                Adicione <code>data-role="tagsinput"</code> ao campo de input para transformá-lo automaticamente em um campo de tags.
              </Description>
              <div className="tags-default">
                <input type="text" defaultValue="Amsterdam,Washington,Sydney" data-role="tagsinput" placeholder="adicionar tags" />
              </div>
            </Col>
            <Col md={6}>
              <SmallTitle>Valor múltiplo real</SmallTitle>
              <Description>
                Use um <code>&lt;select multiple /&gt;</code> como elemento de input para o campo de tags e obtenha suporte real a múltiplos valores.
                Em vez de uma string separada por vírgulas, os valores serão definidos em um array. Os elementos <code>&lt;option /&gt;</code> existentes
                serão definidos automaticamente como tags. Isso também torna possível criar tags contendo vírgula.
              </Description>
              <div className="m-b-0">
                <select multiple data-role="tagsinput" defaultValue={tagCities.map((option: SelectOption) => option.value)}>
                  {renderOptions(tagCities)}
                </select>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function SwitcherySection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle className="m-t-0 m-b-20 header-title">Switchery</SectionTitle>
          <Row>
            <Col md={4} lg={4}>
              <SmallTitle>Básico</SmallTitle>
              <Description>
                Adicione o atributo <code>data-plugin="switchery" data-color="@colors"</code> ao seu elemento de input e ele será convertido em switch.
              </Description>
              <div className="switchery-demo">{switcheryBasicItems.map((item) => <SwitcheryInput item={item} key={item.color} />)}</div>
            </Col>
            <Col md={4} lg={4}>
              <SmallTitle>Tamanhos</SmallTitle>
              <Description>
                Adicione o atributo <code>data-size="small",data-size="large"</code> ao seu elemento de input e ele será convertido em switch.
              </Description>
              <div>{switcherySizeItems.map((item) => <SwitcheryInput item={item} key={`${item.color}-${item.size ?? 'default'}`} />)}</div>
            </Col>
            <Col md={4} lg={4}>
              <SmallTitle>Cor secundária</SmallTitle>
              <Description>
                Adicione o atributo <code>data-color="@color" data-secondary-color="@color"</code> ao seu elemento de input e ele será convertido em switch.
              </Description>
              <div>{switcherySecondaryItems.map((item) => <SwitcheryInput item={item} key={`${item.color}-${item.secondaryColor}`} />)}</div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function MultipleSelectAndSelect2Section(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <Row>
            <Col md={6}>
              <SectionTitle className="m-t-0 m-b-30 header-title">Multiple Select</SectionTitle>
              <SmallTitle>Padrão</SmallTitle>
              <Description className="text-muted m-b-15 font-13">
                Use um <code>&lt;select multiple /&gt;</code> como seu elemento de input para um campo de tags e obtenha suporte real a múltiplos valores.
              </Description>
              <select multiple className="multi-select" id="my_multi_select1" name="my_multi_select1[]" data-plugin="multiselect" defaultValue={['Philadelphia Eagles', 'Washington Redskins', 'Atlanta Falcons']}>
                {renderOptions(teamOptions)}
              </select>

              <SmallTitle className="m-t-30">Opções agrupadas</SmallTitle>
              <Description className="text-muted m-b-15 font-13">
                Use um <code>&lt;select multiple /&gt;</code> como seu elemento de input para um campo de tags e obtenha suporte real a múltiplos valores.
              </Description>
              <select multiple className="multi-select" id="my_multi_select2" name="my_multi_select2[]" data-plugin="multiselect" data-selectable-optgroup="true">
                {renderOptionGroups(groupedTeamOptions)}
              </select>

              <SmallTitle className="m-t-30">Com busca</SmallTitle>
              <Description className="text-muted m-b-15 font-13">
                Use um <code>&lt;select multiple /&gt;</code> como seu elemento de input para um campo de tags e obtenha suporte real a múltiplos valores.
              </Description>
              <select name="country" className="multi-select" multiple id="my_multi_select3">
                {renderOptions(countryOptions)}
              </select>
            </Col>

            <Col md={6}>
              <SectionTitle className="m-t-0 m-b-30 header-title">Select2</SectionTitle>
              <SmallTitle>Seleção única</SmallTitle>
              <select className="form-control select2" defaultValue="Select">
                <option>Select</option>
                {renderOptionGroups(timeZoneGroups)}
              </select>

              <SmallTitle className="m-t-30">Seleção múltipla</SmallTitle>
              <select className="select2 select2-multiple" multiple data-placeholder="Escolha ...">
                {renderOptionGroups(timeZoneGroups)}
              </select>

              <SmallTitle className="m-t-30">Limitando o número de seleções</SmallTitle>
              <select className="select2-limiting" multiple>
                {renderOptionGroups(timeZoneGroups)}
              </select>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function BootstrapSelectSection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle>Bootstrap-select</SectionTitle>
          <Row>
            <Col md={4}>
              <div className="p-20">
                <Description className="text-muted m-b-15 font-13">
                  Crie seu <code>&lt;select&gt;</code> com a classe <code>.selectpicker</code>.
                </Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">Também funciona com grupos de opções:</Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: [], optgroups: bootstrapSelectGroups }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">Você também pode mostrar o ícone de marcação em um <code>select</code> único com a classe <code>show-tick</code>:</Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions, showTick: true }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">E com selects múltiplos:</Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions, multiple: true }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">
                  Você pode limitar o número de elementos que podem ser selecionados com o atributo <code>data-max-options</code>.
                  Isso também funciona para grupos de opções.
                </Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions, multiple: true, maxOptions: 2, className: 'm-b-0' }} />
              </div>
            </Col>

            <Col md={4}>
              <div className="p-20">
                <Description className="text-muted m-b-15 font-13">
                  Você pode limitar o número de elementos que podem ser selecionados com o atributo <code>data-max-options</code>.
                  Isso também funciona para grupos de opções.
                </Description>
                {bootstrapSelectColorDemos.map((demo: BootstrapSelectDemo) => (
                  <div className="formAdvancedPage__selectSpacer" key={demo.dataStyle}>
                    <BootstrapSelect demo={demo} />
                  </div>
                ))}

                <Description className="text-muted m-b-15 m-t-30 font-13">Adicione um ícone a uma opção com o atributo <code>data-icon</code>:</Description>
                <BootstrapSelect demo={{ ...bootstrapSelectIconDemo, className: 'm-b-0' }} />
              </div>
            </Col>

            <Col md={4}>
              <div className="p-20">
                <Description className="text-muted m-b-15 font-13">
                  Você pode adicionar um campo de busca passando o atributo <code>data-live-search="true"</code>:
                </Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectSearchOptions, liveSearch: true }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">
                  Você também pode usar o atributo <code>title</code> como uma alternativa para exibir quando a opção estiver selecionada:
                </Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectTitledOptions, liveSearch: true }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">
                  Usando o atributo <code>data-selected-text-format</code> em um <code>multiple select</code>, você pode especificar como a seleção é exibida.
                </Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions, multiple: true, selectedTextFormat: 'count' }} />
                <div className="formAdvancedPage__doubleBreak" />
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectCountOptions, multiple: true, selectedTextFormat: 'count > 3' }} />

                <Description className="text-muted m-b-15 m-t-30 font-13">Adicione o atributo <code>disabled</code> ao select para aplicar a classe <code>disabled</code>.</Description>
                <BootstrapSelect demo={{ dataStyle: 'btn-white', options: bootstrapSelectOptions, disabled: true, className: 'm-b-0' }} />
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function FileStyleSection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle>Bootstrap FileStyle</SectionTitle>
          <Description className="text-muted m-b-15 font-13">
            Você pode limitar o número de elementos que podem ser selecionados com o atributo <code>data-max-options</code>.
            Isso também funciona para grupos de opções.
          </Description>
          <Row>
            <Col md={6}>
              <div className="p-20">
                <PreventSubmitForm>{fileStyleLeftDemos.map((demo, index) => <FileStyleInput demo={demo} index={index} key={demo.label} />)}</PreventSubmitForm>
              </div>
            </Col>
            <Col md={6}>
              <div className="p-20">
                <PreventSubmitForm>{fileStyleRightDemos.map((demo, index) => <FileStyleInput demo={demo} index={index} key={demo.label} />)}</PreventSubmitForm>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function TouchSpinSection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle>Bootstrap TouchSpin</SectionTitle>
          <Description className="text-muted font-13">
            Você pode limitar o número de elementos que podem ser selecionados com o atributo <code>data-max-options</code>.
            Isso também funciona para grupos de opções.
          </Description>
          <Row>
            <Col md={6}>
              <div className="p-20">
                <PreventSubmitForm>
                  {touchSpinLeftInputs.map((input, index) => <TouchSpinInput input={input} isLast={index === touchSpinLeftInputs.length - 1} key={input.name} />)}
                </PreventSubmitForm>
              </div>
            </Col>
            <Col md={6}>
              <div className="p-20">
                <PreventSubmitForm>
                  {touchSpinRightInputs.slice(0, 3).map((input) => <TouchSpinInput input={input} key={input.name} />)}
                  <TouchSpinButtonGroupInput input={touchSpinRightInputs[3]} />
                </PreventSubmitForm>
              </div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}

export function MaxLengthSection(): JSX.Element {
  return (
    <Row>
      <Col>
        <CardBox className="formAdvancedPage__card">
          <SectionTitle>Bootstrap MaxLength</SectionTitle>
          <Row>
            <Col md={6}>
              <div className="p-20">{maxLengthLeftInputs.map((input) => <MaxLengthInput input={input} key={input.id} />)}</div>
            </Col>
            <Col md={6}>
              <div className="p-20">{maxLengthRightInputs.map((input) => <MaxLengthInput input={input} key={input.id} />)}</div>
            </Col>
          </Row>
        </CardBox>
      </Col>
    </Row>
  );
}
