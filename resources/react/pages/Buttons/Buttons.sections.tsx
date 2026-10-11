import { useEffect, useRef, useState } from 'react';
import './Buttons.css';
import Button, { type ButtonProps, type ButtonTone } from '@/components/UI/Button/Button';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import {
  defaultButtons,
  dropdownMenuItems,
  outlineButtons,
  roundedButtons,
  socialButtons,
  type ButtonsSocialItem,
  type ButtonsVariantItem
} from './Buttons.data';

function joinClassNames(...items: Array<string | false | null | undefined>): string {
  return items.filter(Boolean).join(' ');
}

interface SectionProps {
  title: string;
  description: React.JSX.Element | string;
  children: React.ReactNode;
}

export function ButtonsDemoSection({ title, description, children }: SectionProps): React.JSX.Element {
  return (
    <div className="buttonsPage__section">
      <h4 className="buttonsPage__sectionTitle header-title m-t-0">
        <b>{title}</b>
      </h4>
      <p className="buttonsPage__sectionDescription text-muted m-b-30 font-13">{description}</p>
      {children}
    </div>
  );
}

interface DropdownDemoProps {
  tone: ButtonTone;
  label: string;
  split?: boolean;
  direction?: 'down' | 'up';
}

export function DropdownDemo({ tone, label, split = false, direction = 'down' }: DropdownDemoProps): React.JSX.Element {
  const [open, setOpen] = useState<boolean>(false);
  const containerRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    function handlePointerDown(event: MouseEvent): void {
      if (!containerRef.current?.contains(event.target as Node)) {
        setOpen(false);
      }
    }

    document.addEventListener('mousedown', handlePointerDown);
    return () => {
      document.removeEventListener('mousedown', handlePointerDown);
    };
  }, []);

  return (
    <div
      ref={containerRef}
      className={joinClassNames('buttonsPage__dropdown', 'btn-group', direction === 'up' ? 'dropup' : 'dropdown', open ? 'open' : '')}
    >
      {split ? (
        <>
          <Button tone={tone} label={label} />
          <Button tone={tone} className="buttonsPage__dropdownToggle buttonsPage__dropdownToggle--split" aria-label="Abrir menu" onClick={() => setOpen((value: boolean) => !value)}>
            <i className="caret" />
          </Button>
        </>
      ) : (
        <Button tone={tone} className="buttonsPage__dropdownToggle" onClick={() => setOpen((value: boolean) => !value)}>
          {label} <span className="caret" />
        </Button>
      )}

      <ul className={joinClassNames('dropdown-menu', open ? 'dropdown-menu--open' : '')} role="menu">
        {dropdownMenuItems.map((item: string, index: number) => (
          <li key={item}>
            {index === 3 ? <div className="divider" /> : null}
            <a href="#" onClick={(event: React.MouseEvent<HTMLAnchorElement>) => event.preventDefault()}>
              {item}
            </a>
          </li>
        ))}
      </ul>
    </div>
  );
}

function renderButtonSet(
  items: ButtonsVariantItem[],
  options?: Omit<Extract<ButtonProps, { href?: undefined }>, 'tone' | 'label' | 'href'>
): React.ReactNode {
  return items.map((item: ButtonsVariantItem) => (
    <Button key={`${item.tone}-${item.label}`} tone={item.tone} label={item.label} {...options} />
  ));
}

export function ButtonsShowcase(): React.JSX.Element {
  return (
    <section className="buttonsPage" aria-label="Buttons">
      <div className="buttonsPage__container">
        <div className="buttonsPage__row">
          <div className="buttonsPage__col buttonsPage__col--full">
            <div className="buttonsPage__pageTitleRow">
              <h4 className="page-title buttonsPage__pageTitle">Botões</h4>
              <PageBreadcrumb
                items={[
                  { label: 'Interface', href: '#' },
                  { label: 'UI Kit', href: '#' },
                  { label: 'Botões' }
                ]}
              />
            </div>
          </div>
        </div>

        <div className="buttonsPage__row">
          <div className="buttonsPage__col buttonsPage__col--full">
            <div className="card-box buttonsPage__card">
              <div className="buttonsPage__row">
                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botões padrão"
                    description={
                      <>
                        Use as classes de botão em elementos <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou{' '}
                        <code>&lt;input&gt;</code>.
                      </>
                    }
                  >
                    <div className="button-list">{renderButtonSet(defaultButtons)}</div>
                  </ButtonsDemoSection>
                </div>

                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botão arredondado"
                    description={
                      <>
                        Adicione <code>.btn-rounded</code> ao botão padrão para obter cantos arredondados.
                      </>
                    }
                  >
                    <div className="button-list buttonsPage__roundedDemo">
                      {roundedButtons.slice(0, 7).map((item: ButtonsVariantItem) => (
                        <Button key={`${item.tone}-${item.label}`} tone={item.tone} label={item.label} rounded />
                      ))}
                      <div className="buttonsPage__lineBreak" />
                      {roundedButtons.slice(7).map((item: ButtonsVariantItem) => (
                        <Button key={`${item.tone}-${item.label}`} tone={item.tone} label={item.label} rounded />
                      ))}
                    </div>
                  </ButtonsDemoSection>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="buttonsPage__row">
          <div className="buttonsPage__col buttonsPage__col--full">
            <div className="card-box buttonsPage__card">
              <div className="buttonsPage__row">
                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botões com contorno"
                    description={
                      <>
                        Use a classe <code>.btn-custom</code> para criar rapidamente um botão com contorno.
                      </>
                    }
                  >
                    <div className="button-list">{renderButtonSet(outlineButtons, { outline: true })}</div>
                  </ButtonsDemoSection>
                </div>

                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botões com contorno arredondado"
                    description={
                      <>
                        Adicione <code>.btn-custom .btn-rounded</code> para obter um botão arredondado com contorno.
                      </>
                    }
                  >
                    <div className="button-list buttonsPage__roundedDemo">
                      {outlineButtons.slice(0, 7).map((item: ButtonsVariantItem) => (
                        <Button key={`${item.tone}-${item.label}`} tone={item.tone} label={item.label} outline rounded />
                      ))}
                      <div className="buttonsPage__lineBreak" />
                      {outlineButtons.slice(7).map((item: ButtonsVariantItem) => (
                        <Button key={`${item.tone}-${item.label}`} tone={item.tone} label={item.label} outline rounded />
                      ))}
                    </div>
                  </ButtonsDemoSection>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="buttonsPage__row">
          <div className="buttonsPage__col buttonsPage__col--full">
            <div className="card-box buttonsPage__card">
              <div className="buttonsPage__row">
                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botões com rótulo"
                    description={
                      <>
                        Use as classes de botão em elementos <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou{' '}
                        <code>&lt;input&gt;</code>. Depois adicione um <code>&lt;span&gt;</code> com a classe <code>.btn-label</code> e
                        qualquer <code>ícone</code> dentro. Se quiser o ícone à direita, use a classe <code>.btn-label-right</code> no{' '}
                        <code>&lt;span&gt;</code>.
                      </>
                    }
                  >
                    <div className="button-list">
                      <Button tone="success" label="Sucesso" labelIconClass="fa fa-check" />
                      <Button tone="danger" label="Perigo" labelIconClass="fa fa-times" />
                      <Button tone="info" label="Info" labelIconClass="fa fa-exclamation" />
                      <Button tone="warning" label="Aviso" labelIconClass="fa fa-warning" />
                      <div className="buttonsPage__lineBreak" />
                      <Button tone="default" label="Esquerda" labelIconClass="fa fa-arrow-left" />
                      <Button tone="default" label="Direita" labelIconClass="fa fa-arrow-right" labelRight />
                    </div>
                  </ButtonsDemoSection>
                </div>

                <div className="buttonsPage__col buttonsPage__col--half">
                  <ButtonsDemoSection
                    title="Botões com rótulo arredondado"
                    description={
                      <>
                        Use as classes de botão em elementos <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou{' '}
                        <code>&lt;input&gt;</code>. Depois adicione um <code>&lt;span&gt;</code> com a classe <code>.btn-label</code> e
                        qualquer <code>ícone</code> dentro. Se quiser o ícone à direita, use a classe <code>.btn-label-right</code> no{' '}
                        <code>&lt;span&gt;</code>.
                      </>
                    }
                  >
                    <div className="button-list">
                      <Button tone="success" label="Sucesso" labelIconClass="fa fa-check" rounded />
                      <Button tone="danger" label="Perigo" labelIconClass="fa fa-times" rounded />
                      <Button tone="info" label="Info" labelIconClass="fa fa-exclamation" rounded />
                      <Button tone="warning" label="Aviso" labelIconClass="fa fa-warning" rounded />
                      <div className="buttonsPage__lineBreak" />
                      <Button tone="default" label="Esquerda" labelIconClass="fa fa-arrow-left" rounded />
                      <Button tone="default" label="Direita" labelIconClass="fa fa-arrow-right" labelRight rounded />
                    </div>
                  </ButtonsDemoSection>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="buttonsPage__row">
        <div className="buttonsPage__col buttonsPage__col--full">
          <div className="card-box buttonsPage__card">
            <div className="buttonsPage__row">
              <div className="buttonsPage__col buttonsPage__col--third">
                <ButtonsDemoSection
                  title="Largura dos botões"
                  description={
                    <>
                      Crie botões com largura mínima adicionando <code>.w-xs</code>, <code>.w-sm</code>, <code>.w-md</code> ou
                      <br />
                      <code>.w-lg</code>.
                    </>
                  }
                >
                  <div className="button-list">
                    <Button tone="primary" label="Xs" width="xs" />
                    <Button tone="purple" label="Pequeno" width="sm" />
                    <Button tone="info" label="Médio" width="md" />
                    <Button tone="warning" label="Grande" width="lg" />
                  </div>
                </ButtonsDemoSection>
              </div>

              <div className="buttonsPage__col buttonsPage__col--third">
                <ButtonsDemoSection
                  title="Tamanhos dos botões"
                  description={
                    <>
                      Adicione <code>.btn-lg</code>, <code>.btn-sm</code> ou <code>.btn-xs</code> para tamanhos adicionais.
                    </>
                  }
                >
                  <div className="button-list">
                    <Button tone="primary" label="Grande" size="lg" className="buttonsPage__sizeButton buttonsPage__sizeButton--lg" />
                    <Button tone="danger" label="Normal" className="buttonsPage__sizeButton buttonsPage__sizeButton--md" />
                    <Button tone="success" label="Pequeno" size="sm" className="buttonsPage__sizeButton buttonsPage__sizeButton--sm" />
                    <Button tone="purple" label="Extra pequeno" size="xs" className="buttonsPage__sizeButton buttonsPage__sizeButton--xs" />
                  </div>
                </ButtonsDemoSection>
              </div>

              <div className="buttonsPage__col buttonsPage__col--third">
                <ButtonsDemoSection
                  title="Botões desativados"
                  description={
                    <>
                      Adicione o atributo <code>disabled</code> aos botões <code>&lt;button&gt;</code>.
                    </>
                  }
                >
                  <div className="button-list">
                    <Button tone="info" label="Info" disabled />
                    <Button tone="purple" label="Roxo" disabled />
                    <Button tone="pink" label="Rosa" disabled />
                    <Button tone="inverse" label="Inverso" disabled />
                  </div>
                </ButtonsDemoSection>
              </div>
            </div>
          </div>
        </div>
        </div>

        <div className="buttonsPage__row">
        <div className="buttonsPage__col buttonsPage__col--half">
          <div className="card-box buttonsPage__card">
            <ButtonsDemoSection title="Botões com ícone" description="Botão apenas com ícone.">
              <div className="button-list buttonsPage__iconDemo">
                <Button tone="default" iconOnly leftIconClass="fa fa-heart-o" />
                <Button tone="danger" iconOnly leftIconClass="fa fa-remove" />
                <Button tone="purple" iconOnly leftIconClass="fa fa-music" />
                <Button tone="primary" iconOnly leftIconClass="fa fa-star" />
                <Button tone="success" iconOnly leftIconClass="fa fa-thumbs-o-up" />
                <Button tone="info" iconOnly leftIconClass="fa fa-keyboard-o" />
                <Button tone="warning" iconOnly leftIconClass="fa fa-wrench" />
                <div className="buttonsPage__lineBreak" />
                <Button tone="default" label="Curtir" leftIconClass="fa fa-heart" />
                <Button tone="inverse" label="Compartilhar" leftIconClass="fa fa-envelope-o" />
                <Button tone="warning" label="Lançar" leftIconClass="fa fa-rocket" />
                <Button tone="info" label="Hospedagem em nuvem" leftIconClass="fa fa-cloud" />
                <Button tone="pink" label="Reservar voo" rightIconClass="fa fa-plane" />
                <Button tone="purple" label="Doar dinheiro" rightIconClass="fa fa-money" />
              </div>
            </ButtonsDemoSection>
          </div>

          <div className="card-box buttonsPage__card">
            <ButtonsDemoSection
              title="Botões em bloco"
              description={
                <>
                  Crie botões em nível de bloco adicionando <code>.btn-block</code>.
                </>
              }
            >
              <div className="buttonsPage__blockButtons">
                <Button tone="primary" label="Botão em bloco" size="lg" block />
                <Button tone="pink" label="Botão em bloco" size="md" block />
                <Button tone="success" label="Botão em bloco" size="sm" block />
              </div>
            </ButtonsDemoSection>
          </div>
        </div>

        <div className="buttonsPage__col buttonsPage__col--half">
          <div className="card-box buttonsPage__card">
            <ButtonsDemoSection
              title="Grupo de botões"
              description={
                <>
                  Agrupe uma série de botões <code>.btn</code> dentro de <code>.btn-group</code>.
                </>
              }
            >
              <div className="buttonsPage__groupDemo">
                <div className="btn-group m-b-20">
                  <Button tone="white" label="Esquerda" />
                  <Button tone="white" label="Meio" />
                  <Button tone="white" label="Direita" />
                </div>

                <br className="buttonsPage__groupBreak" />

                <div className="btn-group btn-group-justified m-b-20">
                  <Button href="#" tone="white" label="Esquerda" onClick={(event) => event.preventDefault()} />
                  <Button href="#" tone="white" label="Meio" onClick={(event) => event.preventDefault()} />
                  <Button href="#" tone="white" label="Direita" onClick={(event) => event.preventDefault()} />
                </div>

                <div className="btn-group m-b-20">
                  <Button tone="white" label="1" />
                  <Button tone="white" label="2" />
                  <Button tone="white" label="3" />
                  <Button tone="white" label="4" />
                </div>

                <div className="btn-group m-b-20">
                  <Button tone="white" label="5" />
                  <Button tone="white" label="6" />
                  <Button tone="white" label="7" />
                </div>

                <div className="btn-group m-b-20">
                  <Button tone="white" label="8" />
                </div>

                <br className="buttonsPage__groupBreak" />

                <div className="btn-group m-b-20">
                  <Button tone="white" label="1" />
                  <Button tone="white" label="2" />
                  <Button tone="white" label="3" />
                  <DropdownDemo tone="white" label="Dropdown" />
                </div>

                <div className="buttonsPage__row">
                  <div className="buttonsPage__col buttonsPage__col--quarter">
                    <div className="btn-group-vertical m-b-20">
                      <Button tone="white" label="Topo" />
                      <Button tone="white" label="Meio" />
                      <Button tone="white" label="Base" />
                    </div>
                  </div>
                  <div className="buttonsPage__col buttonsPage__col--quarter">
                    <div className="btn-group-vertical m-b-20">
                      <Button tone="white" label="Botão 1" />
                      <Button tone="white" label="Botão 2" />
                      <DropdownDemo tone="white" label="Botão 3" />
                    </div>
                  </div>
                </div>
              </div>
            </ButtonsDemoSection>
          </div>
        </div>
        </div>

        <div className="buttonsPage__row">
        <div className="buttonsPage__col buttonsPage__col--full">
          <div className="card-box buttonsPage__card">
            <div className="buttonsPage__row">
              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Dropdown"
                  description="Transforme um botão em um acionador de dropdown com algumas mudanças básicas na marcação."
                >
                  <div className="buttonsPage__demoList">
                    <DropdownDemo tone="default" label="Dropdown" />
                    <DropdownDemo tone="info" label="Dropdown" />
                    <DropdownDemo tone="pink" label="Dropdown" />
                    <DropdownDemo tone="purple" label="Dropdown" />
                  </div>
                </ButtonsDemoSection>
              </div>

              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Dropdown com botão dividido"
                  description="Transforme um botão em um acionador de dropdown com algumas mudanças básicas na marcação."
                >
                  <div className="buttonsPage__demoList">
                    <DropdownDemo tone="success" label="Dropdown" split />
                    <DropdownDemo tone="primary" label="Dropdown" split />
                    <DropdownDemo tone="default" label="Dropdown" split />
                  </div>
                </ButtonsDemoSection>
              </div>
            </div>
          </div>
        </div>
        </div>

        <div className="buttonsPage__row">
        <div className="buttonsPage__col buttonsPage__col--full">
          <div className="card-box buttonsPage__card">
            <div className="buttonsPage__row">
              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Dropup"
                  description={
                    <>
                      Menus dropdown podem ser alterados para expandir para cima, em vez de para baixo, adicionando <code>.dropup</code> ao elemento pai.
                    </>
                  }
                >
                  <div className="buttonsPage__demoList">
                    <DropdownDemo tone="primary" label="Dropup" direction="up" />
                    <DropdownDemo tone="warning" label="Dropup" direction="up" />
                    <DropdownDemo tone="success" label="Dropup" direction="up" />
                    <DropdownDemo tone="default" label="Dropup" direction="up" />
                  </div>
                </ButtonsDemoSection>
              </div>

              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Botões de upload de arquivo"
                  description={
                    <>
                      Use as classes de botão em elementos <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou <code>&lt;input&gt;</code>.
                    </>
                  }
                >
                  <div className="buttonsPage__demoList">
                    <label className="fileupload button button--primary waves-effect waves-light">
                      <span>Enviar</span>
                      <input type="file" className="upload" />
                    </label>
                    <label className="fileupload button button--purple waves-effect waves-light">
                      <span>
                        <i className="ion-upload m-r-5" />
                        Enviar
                      </span>
                      <input type="file" className="upload" />
                    </label>
                  </div>
                </ButtonsDemoSection>
              </div>
            </div>
          </div>
        </div>
        </div>

        <div className="buttonsPage__row">
        <div className="buttonsPage__col buttonsPage__col--full">
          <div className="card-box buttonsPage__card">
            <div className="buttonsPage__row">
              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Botões sociais com rótulo"
                  description={
                    <>
                      Use a classe <code>.btn-@yoursocial</code> no elemento pai.
                    </>
                  }
                >
                  <div className="button-list">
                    {socialButtons.map((item: ButtonsSocialItem) => (
                      <Button key={item.key} tone={item.key} label={item.label} labelIconClass={item.iconClass} />
                    ))}
                  </div>
                </ButtonsDemoSection>
              </div>

              <div className="buttonsPage__col buttonsPage__col--half">
                <ButtonsDemoSection
                  title="Botões sociais"
                  description={
                    <>
                      Use a classe <code>.btn-@yoursocial</code> no elemento pai.
                    </>
                  }
                >
                  <div className="button-list">
                    {socialButtons.map((item: ButtonsSocialItem) => (
                      <Button key={`${item.key}-icon`} tone={item.key} leftIconClass={item.iconClass} />
                    ))}

                    <div className="buttonsPage__lineBreak" />

                    {socialButtons.map((item: ButtonsSocialItem) => (
                      <Button key={`${item.key}-text`} tone={item.key} label={item.label} leftIconClass={item.iconClass} />
                    ))}
                  </div>
                </ButtonsDemoSection>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </section>
  );
}
