export interface ChartHandle {
  resize?: () => void;
  destroy: () => void;
}
export type ChartMount = (element: HTMLDivElement) => ChartHandle;

