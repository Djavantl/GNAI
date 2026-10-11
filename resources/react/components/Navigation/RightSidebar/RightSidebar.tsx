import './RightSidebar.css';
import type { ContactItem } from './RightSidebar.data';
import { contacts } from './RightSidebar.data';

interface RightSidebarProps {
  isOpen: boolean;
}

export default function RightSidebar({ isOpen }: RightSidebarProps): React.JSX.Element {
  return (
    <aside className={`rightSidebar side-bar right-bar ${isOpen ? 'rightSidebar--open' : ''}`} aria-label="Barra lateral direita">
      <h4 className="rightSidebar__title text-center">Chat</h4>
      <div className="rightSidebar__contactList contact-list">
        <ul className="rightSidebar__contacts list-group contacts-list">
          {contacts.map((contact: ContactItem) => (
            <li key={contact.name} className="rightSidebar__item list-group-item">
              <a href="/" onClick={(event) => event.preventDefault()}>
                <div className="rightSidebar__avatar avatar">
                  <img src={contact.avatar} alt="" />
                </div>
                <span className="rightSidebar__name name">{contact.name}</span>
                <i className={`fa fa-circle ${contact.status}`} aria-hidden="true" />
              </a>
              <span className="clearfix" />
            </li>
          ))}
        </ul>
      </div>
    </aside>
  );
}
