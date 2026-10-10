interface SidebarScrollbarProps {
  visible: boolean;
  thumbHeight: number;
  thumbTop: number;
}

export default function SidebarScrollbar({
  visible,
  thumbHeight,
  thumbTop
}: SidebarScrollbarProps): JSX.Element {
  return (
    <div className={`sidebarPrimary__scrollbar ${visible && thumbHeight > 0 ? 'sidebarPrimary__scrollbar--visible' : ''}`} aria-hidden="true">
      <div
        className="sidebarPrimary__scrollbarThumb"
        style={{
          height: `${thumbHeight}px`,
          transform: `translateY(${thumbTop}px)`
        }}
      />
    </div>
  );
}
