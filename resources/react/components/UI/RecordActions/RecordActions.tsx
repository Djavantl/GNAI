import "./RecordActions.css";
export default function RecordActions({
  name,
  onEdit,
  onDelete,
}: {
  name: string;
  onEdit?: () => void;
  onDelete?: () => void;
}): React.JSX.Element {
  return (
    <span className="recordActions">
      <a href="#" aria-label={`Editar ${name}`} onClick={onEdit}>
        <i className="md md-edit" aria-hidden="true" />
      </a>{" "}
      <a href="#" aria-label={`Excluir ${name}`} onClick={onDelete}>
        <i className="md md-close" aria-hidden="true" />
      </a>
    </span>
  );
}
