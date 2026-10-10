import './DashboardTwoChatCard.css';
import type { DashboardTwoChatCurrentUser, DashboardTwoChatMessage } from './DashboardTwoChatCard.types';
import { useDashboardTwoChat } from './useDashboardTwoChat';
import CardBox from '@/Components/UI/CardBox/CardBox';

interface DashboardTwoChatCardProps {
  composerButtonLabel?: string;
  currentUser?: DashboardTwoChatCurrentUser;
  title: string;
  inputPlaceholder?: string;
  initialMessages: DashboardTwoChatMessage[];
  onEmptyMessage?: () => void;
}

export default function DashboardTwoChatCard({
  composerButtonLabel = 'Enviar',
  currentUser,
  inputPlaceholder = 'Digite sua mensagem',
  title,
  initialMessages,
  onEmptyMessage
}: DashboardTwoChatCardProps): JSX.Element {
  const {
    messages,
    value,
    setValue,
    sendMessage,
    listRef,
    inputRef,
    thumbHeight,
    thumbTop,
    isVisible
  } = useDashboardTwoChat(initialMessages, currentUser, onEmptyMessage);

  return (
    <CardBox className="dashboardTwoChatCard">
      <h4 className="dashboardTwoChatCard__title m-t-0 m-b-20 header-title">
        <b>{title}</b>
      </h4>

      <div className="dashboardTwoChatCard__conversation chat-conversation">
        <div className="dashboardTwoChatCard__listPane">
          <ul ref={listRef} className="dashboardTwoChatCard__list conversation-list">
            {messages.map((message: DashboardTwoChatMessage) => (
              <li key={message.id} className={`clearfix ${message.isCurrentUser ? 'odd' : ''}`}>
                <div className="dashboardTwoChatCard__avatar chat-avatar">
                  <img src={message.avatar} alt={message.author} />
                  <i>{message.time}</i>
                </div>
                <div className="dashboardTwoChatCard__text conversation-text">
                  <div className="dashboardTwoChatCard__bubble ctext-wrap">
                    <i>{message.author}</i>
                    <p>{message.text}</p>
                  </div>
                </div>
              </li>
            ))}
          </ul>

          <div
            className={`dashboardTwoChatCard__scrollbar ${isVisible && thumbHeight > 0 ? 'dashboardTwoChatCard__scrollbar--visible' : ''}`}
            aria-hidden="true"
          >
            <div
              className="dashboardTwoChatCard__scrollbarThumb"
              style={{
                height: `${thumbHeight}px`,
                transform: `translateY(${thumbTop}px)`
              }}
            />
          </div>
        </div>

        <div className="dashboardTwoChatCard__inputRow">
          <div className="dashboardTwoChatCard__inputColumn chat-inputbar">
            <input
              type="text"
              ref={inputRef}
              className="dashboardTwoChatCard__input form-control chat-input"
              placeholder={inputPlaceholder}
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
          <div className="dashboardTwoChatCard__buttonColumn chat-send">
            <button
              type="button"
              className="dashboardTwoChatCard__button btn btn-md btn-info btn-block waves-effect waves-light"
              onClick={sendMessage}
            >
              {composerButtonLabel}
            </button>
          </div>
        </div>
      </div>
    </CardBox>
  );
}
