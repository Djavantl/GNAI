import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import './Typography.css';

interface SectionTitleProps {
  children: string;
  className?: string;
}

function SectionTitle({ children, className }: SectionTitleProps): JSX.Element {
  return (
    <h4 className={`typographyPage__sectionTitle header-title ${className ?? 'm-t-0'}`}>
      <b>{children}</b>
    </h4>
  );
}

interface DescriptionProps {
  children: React.ReactNode;
  className?: string;
}

function Description({ children, className }: DescriptionProps): JSX.Element {
  return <p className={`typographyPage__description text-muted font-13 ${className ?? 'm-b-15'}`}>{children}</p>;
}

const unorderedItems = [
  'Lorem ipsum dolor sit amet',
  'Consectetur adipiscing elit',
  'Integer molestie lorem at massa',
  'Facilisis in pretium nisl aliquet',
  'Nulla volutpat aliquam velit',
  'Faucibus porta lacus fringilla vel',
  'Aenean sit amet erat nunc',
  'Eget porttitor lorem'
];

const nestedItems = ['Phasellus iaculis neque', 'Purus sodales ultricies', 'Vestibulum laoreet porttitor sem', 'Ac tristique libero volutpat at'];

const contextualTexts = [
  { className: 'text-custom', text: 'Etiam porta sem malesuada magna mollis euismod.' },
  { className: 'text-pink', text: 'Donec ullamcorper nulla non metus auctor fringilla.' },
  { className: 'text-muted', text: 'Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.' },
  { className: 'text-primary', text: 'Nullam id dolor id nibh ultricies vehicula ut id elit.' },
  { className: 'text-success', text: 'Duis mollis, est non commodo luctus, nisi erat porttitor ligula.' },
  { className: 'text-info', text: 'Maecenas sed diam eget risus varius blandit sit amet non magna.' },
  { className: 'text-warning', text: 'Etiam porta sem malesuada magna mollis euismod.' },
  { className: 'text-danger', text: 'Donec ullamcorper nulla non metus auctor fringilla.' },
  { className: 'text-purple', text: 'Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.' }
];

export function HeadingsSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--half typographyPage__headingsColumn">
          <SectionTitle>Cabeçalhos</SectionTitle>
          <Description>
            Todos os cabeçalhos HTML, <code>&lt;h1&gt;</code> até <code>&lt;h6&gt;</code>, estão disponíveis. As classes <code>.h1</code> até <code>.h6</code> também estão disponíveis quando você quer combinar o estilo de um cabeçalho mantendo o texto em linha.
          </Description>
          <h1>h1. Cabeçalho 1</h1>
          <h2>h2. Cabeçalho 2</h2>
          <h3>h3. Cabeçalho 3</h3>
          <h4>h4. Cabeçalho 4</h4>
          <h5>h5. Cabeçalho 5</h5>
          <h6 className="m-b-0">h6. Cabeçalho 6</h6>
        </div>

        <div className="typographyPage__col typographyPage__col--half typographyPage__headingsColumn">
          <SectionTitle>Cabeçalhos estilizados</SectionTitle>
          <Description>
            Crie texto secundário mais leve em qualquer cabeçalho com <code>&lt;small&gt;</code> ou <code>.small</code>.
          </Description>
          <h1>Cabeçalho 1 <small>Texto secundário</small></h1>
          <h2>Cabeçalho 2 <small>Texto secundário</small></h2>
          <h3>Cabeçalho 3 <small>Texto secundário</small></h3>
          <h4>Cabeçalho 4 <small>Texto secundário</small></h4>
          <h5>Cabeçalho 5 <small>Texto secundário</small></h5>
          <h6>Cabeçalho 6 <small>Texto secundário</small></h6>
        </div>
      </div>
    </CardBox>
  );
}

export function InlineTextSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Elementos de texto em linha</SectionTitle>

          <Description className="m-t-15 m-b-5">Destaque um parágrafo adicionando <code>.lead</code>.</Description>
          <p className="lead m-t-0">Seu título vai aqui</p>

          <Description className="m-t-20 m-b-5">Para destacar uma sequência de texto pela relevância em outro contexto, use a tag <code>&lt;mark&gt;</code>.</Description>
          Você pode usar a tag mark para <mark>destacar</mark> texto.

          <Description className="m-t-20 m-b-5">Para indicar blocos de texto que foram excluídos, use a tag <code>&lt;del&gt;</code>.</Description>
          <del>Esta linha de texto deve ser tratada como texto excluído.</del>

          <Description className="m-t-20 m-b-5">Para indicar adições ao documento, use a tag <code>&lt;ins&gt;</code>.</Description>
          <ins>Esta linha de texto deve ser tratada como uma adição ao documento.</ins>

          <Description className="m-t-20 m-b-5">Para enfatizar um trecho de texto com peso maior. <code>&lt;strong&gt;</code></Description>
          <strong>renderizado como texto em negrito</strong>

          <Description className="m-t-20 m-b-5">Para enfatizar um trecho de texto com itálico. <code>&lt;em&gt;</code></Description>
          <em>renderizado como texto em itálico</em>
        </div>

        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Cores contextuais de texto</SectionTitle>
          <Description className="m-t-20 m-b-5">Use classes <code>.text-custom</code>, <code>.text-primary</code> etc. para destacar texto.</Description>
          {contextualTexts.map((item) => <p key={item.className} className={item.className}>{item.text}</p>)}
        </div>
      </div>
    </CardBox>
  );
}

export function AddressesAndQuotesSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Endereços</SectionTitle>
          <Description>Apresente informações de contato do ancestral mais próximo ou de todo o corpo do trabalho. Preserve a formatação terminando todas as linhas com <code>&lt;br&gt;</code>.</Description>
          <address>
            <strong>Twitter, Inc.</strong><br />
            795 Folsom Ave, Suite 600<br />
            San Francisco, CA 94107<br />
            <abbr title="Telefone">T:</abbr> (123) 456-7890
          </address>
          <address>
            <strong>Nome completo</strong><br />
            <a href="mailto:#">first.last@example.com</a>
          </address>
        </div>

        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Citações em bloco</SectionTitle>
          <Description>Adicione um <code>&lt;footer&gt;</code> para identificar a fonte. Envolva o nome da obra de origem com <code>&lt;cite&gt;</code>.</Description>
          <blockquote>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
            <footer>Alguém famoso em <cite title="Título da fonte">Título da fonte</cite></footer>
          </blockquote>
          <Description className="m-b-15 m-t-20">Adicione <code>.blockquote-reverse</code> para uma citação em bloco com conteúdo alinhado à direita.</Description>
          <blockquote className="blockquote-reverse m-b-0">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
            <footer>Alguém famoso em <cite title="Título da fonte">Título da fonte</cite></footer>
          </blockquote>
        </div>
      </div>
    </CardBox>
  );
}

export function ListsSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--third">
          <SectionTitle>Não ordenada</SectionTitle>
          <Description>Uma lista de itens em que a ordem não importa explicitamente.</Description>
          <ul>
            {unorderedItems.map((item) => (
              <li key={item}>
                {item}
                {item === 'Nulla volutpat aliquam velit' ? <ul>{nestedItems.map((nested) => <li key={nested}>{nested}</li>)}</ul> : null}
              </li>
            ))}
          </ul>
        </div>

        <div className="typographyPage__col typographyPage__col--third">
          <SectionTitle>Ordenada</SectionTitle>
          <Description>Uma lista de itens em que a ordem importa explicitamente.</Description>
          <ol>{unorderedItems.map((item) => <li key={item}>{item}</li>)}</ol>
        </div>

        <div className="typographyPage__col typographyPage__col--third">
          <SectionTitle>Sem estilo</SectionTitle>
          <Description>Remova o <code>list-style</code> padrão e a margem esquerda nos itens de lista, apenas nos filhos imediatos. <strong>Isso vale apenas para filhos imediatos</strong>, então listas aninhadas também precisam receber a classe para manter o mesmo comportamento.</Description>
          <ul className="list-unstyled">
            <li>Lorem ipsum dolor sit amet</li>
            <li>Integer molestie lorem at massa
              <ul>
                <li>Phasellus iaculis neque</li>
                <li>Purus sodales ultricies</li>
              </ul>
            </li>
            <li>Faucibus porta lacus fringilla vel</li>
            <li>Eget porttitor lorem</li>
          </ul>
          <h4><b>Em linha</b></h4>
          <Description>Coloque todos os itens de lista em uma única linha com <code>display: inline-block;</code> e um padding leve.</Description>
          <ul className="list-inline m-b-0">
            <li>Lorem ipsum</li>
            <li>Phasellus iaculis</li>
            <li>Nulla volutpat</li>
          </ul>
        </div>
      </div>
    </CardBox>
  );
}

export function DescriptionListsSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Descrição</SectionTitle>
          <Description>Uma lista de termos com suas descrições associadas.</Description>
          <dl className="m-b-0">
            <dt>Listas de descrição</dt>
            <dd>Uma lista de descrição é perfeita para definir termos.</dd>
            <dt>Euismod</dt>
            <dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
            <dd>Donec id elit non mi porta gravida at eget metus.</dd>
            <dt>Malesuada porta</dt>
            <dd>Etiam porta sem malesuada magna mollis euismod.</dd>
          </dl>
        </div>

        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Descrição horizontal</SectionTitle>
          <Description>Faça termos e descrições em <code>&lt;dl&gt;</code> ficarem lado a lado. Começa empilhado como o <code>&lt;dl&gt;</code> padrão, mas quando a barra de navegação expande, estes também expandem.</Description>
          <dl className="dl-horizontal m-b-0">
            <dt>Listas de descrição</dt>
            <dd>Uma lista de descrição é perfeita para definir termos.</dd>
            <dt>Euismod</dt>
            <dd>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</dd>
            <dd>Donec id elit non mi porta gravida at eget metus.</dd>
            <dt>Malesuada porta</dt>
            <dd>Etiam porta sem malesuada magna mollis euismod.</dd>
            <dt>Felis euismod semper eget lacinia</dt>
            <dd>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</dd>
          </dl>
        </div>
      </div>
    </CardBox>
  );
}

export function DropcapAndCodeSection(): JSX.Element {
  return (
    <CardBox className="typographyPage__card typographyPage__card--last">
      <div className="typographyPage__row">
        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Letra capitular</SectionTitle>
          <Description>Use a classe <code>.dropcap</code> para deixar a primeira letra cair no conteúdo do texto.</Description>
          <p><span className="dropcap text-primary">D</span>orem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates, illo, iste itaque voluptas corrupti ratione reprehenderit magni similique? Tempore, quos delectus asperiores libero voluptas quod perferendis! Voluptate, quod illo rerum? Lorem ipsum dolor sit amet.</p>
          <p className="m-b-0"><span className="dropcap text-pink">D</span>orem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates, illo, iste itaque voluptas corrupti ratione reprehenderit magni similique? Tempore, quos delectus asperiores libero voluptas quod perferendis! Voluptate, quod illo rerum? Lorem ipsum dolor sit amet.</p>
        </div>

        <div className="typographyPage__col typographyPage__col--half">
          <SectionTitle>Bloco básico</SectionTitle>
          <Description>Use <code>&lt;pre&gt;</code> para várias linhas de código. Escape sinais de menor e maior no código para renderização correta.</Description>
          <pre>&lt;p&gt;Sample text here...&lt;/p&gt;</pre>
        </div>
      </div>
    </CardBox>
  );
}


export function TypographyPageTitle(): JSX.Element {
  return (
    <div className="typographyPage__row">
      <div className="typographyPage__col typographyPage__col--full">
        <div className="typographyPage__pageTitleRow">
          <h4 className="page-title typographyPage__pageTitle">Tipografia</h4>
          <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'UI Kit', href: '#' }, { label: 'Tipografia' }]} />
        </div>
      </div>
    </div>
  );
}
