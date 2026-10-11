import { useEffect, useRef, useState } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import { joinClassNames } from '@/utils/joinClassNames';
import { animateElementScrollTop } from '@/utils/animateElementScrollTop';
import { useCustomScrollbar } from '@/hooks/useCustomScrollbar';
import { chatItems, friendAvatars, inboxItems, todoItems, transactions } from './ComponentsWidgets.data';
import { Col, CustomScrollRail, Row, SectionHeader } from './ComponentsWidgetsLayout';
import type { SweetAlertMessage } from './ComponentsWidgets.types';

export function InboxCard(): React.JSX.Element {
  const { scrollRef, thumbHeight, thumbTop, isVisible } = useCustomScrollbar<HTMLDivElement>(true);

  return (
    <CardBox>
      <h4 className="m-t-0 m-b-20 header-title"><b>Caixa de entrada</b></h4>
      <div className="componentsWidgetsPage__scrollHost componentsWidgetsPage__scrollHost--inbox">
        <div ref={scrollRef} className="inbox-widget nicescroll mx-box componentsWidgetsPage__scrollBox">
          {inboxItems.map(([avatar, author, text, date]) => (
            <a href="#" key={`${author}-${date}`} onClick={(event) => event.preventDefault()}>
              <div className="inbox-item">
                <div className="inbox-item-img"><img src={`/assets/react/images/users/${avatar}`} className="img-circle" alt="" /></div>
                <p className="inbox-item-author">{author}</p>
                <p className="inbox-item-text">{text}</p>
                <p className="inbox-item-date">{date}</p>
              </div>
            </a>
          ))}
        </div>
        <CustomScrollRail className="componentsWidgetsPage__niceScrollRail" height={380} isVisible={isVisible} thumbHeight={thumbHeight} thumbTop={thumbTop} />
      </div>
    </CardBox>
  );
}

export function ChatCard({ onEmptyMessage }: { onEmptyMessage: () => void }): React.JSX.Element {
  const [messages, setMessages] = useState(chatItems);
  const [value, setValue] = useState<string>('');
  const inputRef = useRef<HTMLInputElement | null>(null);
  const shouldAnimateScrollRef = useRef<boolean>(false);
  const cancelScrollAnimationRef = useRef<(() => void) | null>(null);
  const { scrollRef, thumbHeight, thumbTop, isVisible } = useCustomScrollbar<HTMLUListElement>(true);

  useEffect(() => {
    if (!scrollRef.current || !shouldAnimateScrollRef.current) {
      return;
    }

    shouldAnimateScrollRef.current = false;
    cancelScrollAnimationRef.current?.();
    cancelScrollAnimationRef.current = animateElementScrollTop(scrollRef.current, scrollRef.current.scrollHeight);
  }, [messages, scrollRef]);

  useEffect(() => {
    return () => {
      cancelScrollAnimationRef.current?.();
    };
  }, []);

  function sendMessage(): void {
    const chatText = value.trim();

    if (!chatText) {
      onEmptyMessage();
      inputRef.current?.focus();
      return;
    }

    const now = new Date();
    const hours = now.getHours() % 12 || 12;
    const minutes = now.getMinutes().toString().padStart(2, '0');
    shouldAnimateScrollRef.current = true;
    setMessages((currentMessages) => [...currentMessages, { odd: false, avatar: '/assets/react/images/avatar-1.jpg', alt: 'male', time: `${hours}:${minutes}`, author: 'John Deo', text: chatText }]);
    setValue('');
    inputRef.current?.focus();
  }

  return (
    <CardBox className="componentsWidgetsPage__otherCardFixed">
      <h4 className="m-t-0 m-b-20 header-title"><b>Bate-papo</b></h4>
      <div className="chat-conversation">
        <div className="componentsWidgetsPage__scrollHost componentsWidgetsPage__scrollHost--chat">
          <ul ref={scrollRef} className="conversation-list nicescroll componentsWidgetsPage__chatScrollBox">
            {messages.map((item, index) => (
              <li className={joinClassNames('clearfix', item.odd ? 'odd' : undefined)} key={`${item.author}-${item.time}-${item.text}-${index}`}>
                <div className="chat-avatar"><img src={item.avatar} alt={item.alt} /><i>{item.time}</i></div>
                <div className="conversation-text"><div className="ctext-wrap"><i>{item.author}</i><p>{item.text}</p></div></div>
              </li>
            ))}
          </ul>
          <CustomScrollRail className="componentsWidgetsPage__niceScrollRail" height={332} isVisible={isVisible} thumbHeight={thumbHeight} thumbTop={thumbTop} />
        </div>
        <Row>
          <div className="componentsWidgetsPage__col componentsWidgetsPage__col--chatInput chat-inputbar">
            <input
              type="text"
              ref={inputRef}
              className="form-control chat-input"
              placeholder="Digite seu texto"
              value={value}
              onChange={(event) => setValue(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter') {
                  event.preventDefault();
                  sendMessage();
                }
              }}
            />
          </div>
          <div className="componentsWidgetsPage__col componentsWidgetsPage__col--chatSend chat-send"><button type="button" className="btn btn-md btn-info btn-block waves-effect waves-light" onClick={sendMessage}>Enviar</button></div>
        </Row>
      </div>
    </CardBox>
  );
}

export function TodoCard({ onEmptyTodo }: { onEmptyTodo: () => void }): React.JSX.Element {
  const [items, setItems] = useState(todoItems);
  const [value, setValue] = useState<string>('');
  const inputRef = useRef<HTMLInputElement | null>(null);
  const remaining = items.filter((todo) => !todo.done).length;

  function toggleTodo(id: number): void {
    setItems((currentItems) => currentItems.map((todo) => todo.id === id ? { ...todo, done: !todo.done } : todo));
  }

  function archiveCompleted(): void {
    setItems((currentItems) => currentItems.filter((todo) => !todo.done));
  }

  function addTodo(): void {
    const text = value.trim();

    if (!text) {
      onEmptyTodo();
      inputRef.current?.focus();
      return;
    }

    setItems((currentItems) => [...currentItems, { id: Date.now(), text, done: false }]);
    setValue('');
  }

  return (
    <CardBox>
      <h4 className="m-t-0 m-b-20 header-title"><b>Tarefas</b></h4>
      <div className="todoapp">
        <Row>
          <div className="componentsWidgetsPage__col componentsWidgetsPage__col--half"><h4 id="todo-message"><span id="todo-remaining">{remaining}</span> de <span id="todo-total">{items.length}</span> restantes</h4></div>
          <div className="componentsWidgetsPage__col componentsWidgetsPage__col--half"><button type="button" className="pull-right btn btn-primary btn-sm waves-effect waves-light" onClick={archiveCompleted}>Arquivar</button></div>
        </Row>
        <ul className="list-group no-margn nicescroll todo-list componentsWidgetsPage__todoList" id="todo-list">
          {items.slice().reverse().map((todo) => (
            <li className="list-group-item" key={todo.id}>
              <div className="checkbox checkbox-primary">
                <input className="todo-done" id={`todo-${todo.id}`} type="checkbox" checked={todo.done} onChange={() => toggleTodo(todo.id)} />
                <label htmlFor={`todo-${todo.id}`}>{todo.text}</label>
              </div>
            </li>
          ))}
        </ul>
        <form className="m-t-20" onSubmit={(event) => { event.preventDefault(); addTodo(); }}>
          <Row>
            <div className="componentsWidgetsPage__col componentsWidgetsPage__col--todoInput todo-inputbar">
              <input
                ref={inputRef}
                type="text"
                className="form-control"
                placeholder="Adicionar nova tarefa"
                value={value}
                onChange={(event) => setValue(event.target.value)}
              />
            </div>
            <div className="componentsWidgetsPage__col componentsWidgetsPage__col--todoSend todo-send"><button className="btn-primary btn-md btn-block btn waves-effect waves-light" type="button" onClick={addTodo}>Adicionar</button></div>
          </Row>
        </form>
      </div>
    </CardBox>
  );
}

export function TransactionsCard(): React.JSX.Element {
  return (
    <CardBox>
      <h4 className="m-t-0 m-b-20 header-title"><b>Últimas transações</b></h4>
      <ul className="list-unstyled transaction-list">
        {transactions.map(([icon, text, price, priceClass, date]) => (
          <li key={`${text}-${price}-${date}`}><i className={icon} /><span className="tran-text">{text}</span><span className={joinClassNames('pull-right', priceClass, 'tran-price')}>{price}</span><span className="pull-right text-muted">{date}</span><span className="clearfix" /></li>
        ))}
      </ul>
    </CardBox>
  );
}

function PeopleList({ title, thumbnail = false }: { title: string; thumbnail?: boolean }): React.JSX.Element {
  return (
    <>
      <h4 className="m-t-0 m-b-20 header-title"><b>{title} <span className="text-muted">{title === 'Amigos' ? '(154)' : '(685)'}</span></b></h4>
      <div className="input-group m-b-15">
        <input type="text" className="form-control" placeholder="Pesquisar" />
        <span className="input-group-btn"><button type="button" className="btn waves-effect btn-white"><i className="fa fa-search" /></button></span>
      </div>
      <div className="friend-list text-center">
        {friendAvatars.map((avatar) => (
          <a href="#" key={`${title}-${avatar}`} onClick={(event) => event.preventDefault()}><img src={`/assets/react/images/users/${avatar}`} className={joinClassNames('img-circle thumb-md', thumbnail ? 'img-thumbnail' : undefined)} alt="amigo" /></a>
        ))}
        <a href="#" onClick={(event) => event.preventDefault()}><span className="extra-number">+89</span></a>
      </div>
    </>
  );
}

export function FriendsFollowersColumn(): React.JSX.Element {
  return (
    <>
      <CardBox className="componentsWidgetsPage__peopleCard">
        <PeopleList title="Amigos" />
      </CardBox>
      <CardBox className="componentsWidgetsPage__peopleCard">
        <PeopleList title="Seguidores" thumbnail />
      </CardBox>
    </>
  );
}

export function ProfileCard(): React.JSX.Element {
  return (
    <CardBox className="p-0 componentsWidgetsPage__profileCard">
      <div className="profile-widget text-center">
        <div className="bg-profile bg-custom" />
        <img src="/assets/react/images/users/avatar-3.jpg" className="thumb-lg img-circle img-thumbnail" alt="img" />
        <h4>Jonathan Doe</h4>
        <p className="text-muted"><i className="fa fa-map-marker" /> Londres</p>
        <a href="#" className="btn btn-sm btn-purple m-t-20" onClick={(event) => event.preventDefault()}>Seguir</a>{' '}
        <a href="#" className="btn btn-sm btn-pink m-t-20" onClick={(event) => event.preventDefault()}>Enviar mensagem</a>
        <p className="m-t-10 text-muted p-20">Sobreviveu não apenas a cinco séculos, mas também à transição para a composição eletrônica, permanecendo essencialmente inalterado.</p>
        <ul className="list-inline widget-list clearfix">
          <li className="componentsWidgetsPage__profileStat"><span>2.109</span>Seguidores</li>
          <li className="componentsWidgetsPage__profileStat"><span>596</span>Fotos</li>
          <li className="componentsWidgetsPage__profileStat"><span>902</span>Curtidas</li>
        </ul>
      </div>
    </CardBox>
  );
}

export function OtherWidgetsSection({ onSweetAlert }: { onSweetAlert: (message: SweetAlertMessage) => void }): React.JSX.Element {
  return (
    <>
      <SectionHeader>Outros widgets</SectionHeader>
      <Row>
        <Col size="third"><InboxCard /></Col>
        <Col size="third"><ChatCard onEmptyMessage={() => onSweetAlert('chat')} /></Col>
        <Col size="third"><TodoCard onEmptyTodo={() => onSweetAlert('todo')} /></Col>
      </Row>
      <Row>
        <Col size="third"><TransactionsCard /></Col>
        <Col size="third"><FriendsFollowersColumn /></Col>
        <Col size="third"><ProfileCard /></Col>
      </Row>
    </>
  );
}
