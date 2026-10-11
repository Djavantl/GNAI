export interface DashboardThreeProjectItem {
  assign: string;
  dueDate: string;
  id: number;
  projectName: string;
  startDate: string;
  status: string;
  statusVariant: 'info' | 'success' | 'pink' | 'purple' | 'warning' | 'primary';
}

export interface DashboardThreeProjectsColumns {
  assign: string;
  dueDate: string;
  id: string;
  projectName: string;
  startDate: string;
  status: string;
}
