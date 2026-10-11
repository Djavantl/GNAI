import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import Tabs, { Accordion, type AccordionItem, type TabItem } from '@/components/Navigation/Tabs/Tabs';
import './TabsPage.css';

const first = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.';
const second = 'Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.';
const long = 'Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.';

export function copyContent(order: 'first' | 'second' = 'first'): React.JSX.Element { return <><p>{order === 'first' ? first : second}</p><p>{order === 'first' ? second : first}</p></>; }
export function tabItems(prefix: string): TabItem[] { return [{ id: `${prefix}-home`, label: 'Início', icon: 'fa fa-home', content: copyContent() }, { id: `${prefix}-profile`, label: 'Perfil', icon: 'fa fa-user', content: copyContent('second') }, { id: `${prefix}-messages`, label: 'Mensagens', icon: 'fa fa-envelope-o', content: copyContent() }, { id: `${prefix}-settings`, label: 'Configurações', icon: 'fa fa-cog', content: copyContent('second') }]; }
export function accordionItems(prefix: string): AccordionItem[] { return [1, 2, 3].map((number: number) => ({ id: `${prefix}-${number}`, title: `Item do grupo recolhível #${number}`, content: <p>{long} Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p> })); }

export function Pills({ suffix }: { suffix: string }): React.JSX.Element {
  const items = [1, 2, 3].map((number: number) => ({ id: `pill-${suffix}-${number}`, label: `Aba ${number === 1 ? 'Um' : number === 2 ? 'Dois' : 'Três'}`, content: <div className="tabs__pillContent"><p>{long}</p><img src={`/assets/react/images/small/img${number}.jpg`} alt="" /></div> }));
  return <div className={`tabs__pillPanel tabs__pillPanel--${suffix === 'right' ? 'right' : 'left'}`}><div className="tabs__pillPanelHeading"><h3>Abas em pílulas</h3></div><div className="tabs__pillPanelBody"><Tabs items={items} variant="pills" /></div></div>;
}

export function TabsShowcase(): React.JSX.Element {
  return <section className="tabsPage" aria-label="Abas e accordions">
    <div className="tabsPage__titleRow"><h4 className="tabsPage__title">Abas e acordions</h4><PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'UI Kit', href: '#' }, { label: 'Abas e acordions' }]} /></div>
    <div className="tabsPage__row"><Tabs items={tabItems('basic')} /><Tabs items={tabItems('top')} variant="material" /></div>
    <div className="tabsPage__row"><Tabs items={tabItems('profile')} initialActiveId="profile-profile" variant="bootstrap" /><Tabs items={tabItems('justified')} initialActiveId="justified-profile" variant="justified" /></div>
    <div className="tabsPage__row"><Tabs items={tabItems('vertical')} initialActiveId="vertical-profile" variant="vertical" /><Tabs items={tabItems('vertical-reverse')} variant="vertical-reverse" /></div>
    <div className="tabsPage__row"><Pills suffix="right" /><Pills suffix="left" /></div>
    <div className="tabsPage__row"><Accordion items={accordionItems('normal')} initialActiveId="normal-2" /><Accordion items={accordionItems('joined')} initialActiveId="joined-2" joined /></div>
  </section>;
}
