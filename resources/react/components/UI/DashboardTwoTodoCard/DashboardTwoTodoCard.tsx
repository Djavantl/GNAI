import './DashboardTwoTodoCard.css';
import type { DashboardTwoTodoItem } from './DashboardTwoTodoCard.types';
import { useDashboardTwoTodo } from './useDashboardTwoTodo';
import CardBox from '@/components/UI/CardBox/CardBox';
import ChoiceControl from '@/components/Forms/ChoiceControl/ChoiceControl';

interface DashboardTwoTodoCardProps {
  addButtonLabel?: string;
  archiveButtonLabel?: string;
  inputPlaceholder?: string;
  remainingTextFormatter?: (remaining: number, total: number) => string;
  title: string;
  initialItems: DashboardTwoTodoItem[];
  onEmptyTodo?: () => void;
}

export default function DashboardTwoTodoCard({
  addButtonLabel = 'Adicionar',
  archiveButtonLabel = 'Arquivar',
  inputPlaceholder = 'Adicionar nova tarefa',
  remainingTextFormatter,
  title,
  initialItems,
  onEmptyTodo
}: DashboardTwoTodoCardProps): React.JSX.Element {
  const {
    items,
    value,
    setValue,
    remainingItems,
    toggleItem,
    archiveCompleted,
    addItem,
    scrollRef,
    thumbHeight,
    thumbTop,
    isVisible,
    inputRef
  } = useDashboardTwoTodo(initialItems, onEmptyTodo);

  return (
    <CardBox className="dashboardTwoTodoCard">
      <h4 className="dashboardTwoTodoCard__title m-t-0 m-b-20 header-title">
        <b>{title}</b>
      </h4>

      <div className="dashboardTwoTodoCard__app todoapp">
        <div className="dashboardTwoTodoCard__summaryRow">
          <div className="dashboardTwoTodoCard__summaryColumn">
            <h4 className="dashboardTwoTodoCard__message" id="todo-message">
              {remainingTextFormatter ? (
                remainingTextFormatter(remainingItems, items.length)
              ) : (
                <>
                  <span>{remainingItems}</span> de <span>{items.length}</span> restantes
                </>
              )}
            </h4>
          </div>
          <div className="dashboardTwoTodoCard__archiveColumn">
            <button
              type="button"
              className="dashboardTwoTodoCard__archiveButton btn btn-primary btn-sm waves-effect waves-light"
              onClick={archiveCompleted}
            >
              {archiveButtonLabel}
            </button>
          </div>
        </div>

        <div className="dashboardTwoTodoCard__listPane">
          <ul ref={scrollRef} className="dashboardTwoTodoCard__list list-group no-margn todo-list">
            {items.map((item: DashboardTwoTodoItem) => (
              <li key={item.id} className="list-group-item">
                <ChoiceControl
                  id={`todo-${item.id}`}
                  type="checkbox"
                  tone="primary"
                  label={item.text}
                  checked={item.done}
                  className="dashboardTwoTodoCard__checkbox"
                  inputClassName="dashboardTwoTodoCard__checkboxInput todo-done"
                  labelClassName="dashboardTwoTodoCard__checkboxLabel"
                  onChange={() => toggleItem(item.id)}
                />
              </li>
            ))}
          </ul>

          <div
            className={`dashboardTwoTodoCard__scrollbar ${isVisible && thumbHeight > 0 ? 'dashboardTwoTodoCard__scrollbar--visible' : ''}`}
            aria-hidden="true"
          >
            <div
              className="dashboardTwoTodoCard__scrollbarThumb"
              style={{
                height: `${thumbHeight}px`,
                transform: `translateY(${thumbTop}px)`
              }}
            />
          </div>
        </div>

        <div className="dashboardTwoTodoCard__formRow m-t-20">
          <div className="dashboardTwoTodoCard__formInputColumn todo-inputbar">
            <input
              ref={inputRef}
              type="text"
              className="dashboardTwoTodoCard__input form-control"
              placeholder={inputPlaceholder}
              value={value}
              onChange={(event) => setValue(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter') {
                  event.preventDefault();
                  addItem();
                }
              }}
            />
          </div>
          <div className="dashboardTwoTodoCard__formButtonColumn todo-send">
            <button
              type="button"
              className="dashboardTwoTodoCard__addButton btn-primary btn-md btn-block btn waves-effect waves-light"
              onClick={addItem}
            >
              {addButtonLabel}
            </button>
          </div>
        </div>
      </div>
    </CardBox>
  );
}
