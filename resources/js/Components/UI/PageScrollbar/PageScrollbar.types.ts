export interface PageScrollMetrics {
  top: number;
  height: number;
  scrollTop: number;
  maxScroll: number;
  thumbHeight: number;
  thumbTop: number;
  locked: boolean;
}

export interface PageScrollbarProps {
  belowTopBar?: boolean;
}
