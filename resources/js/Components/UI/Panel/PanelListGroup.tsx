import './Panel.css';

interface PanelListGroupProps {
  items: string[];
}

export default function PanelListGroup({ items }: PanelListGroupProps): JSX.Element {
  return (
    <ul className="panelCard__list list-group">
      {items.map((item: string) => (
        <li key={item} className="panelCard__listItem list-group-item">
          {item}
        </li>
      ))}
    </ul>
  );
}
