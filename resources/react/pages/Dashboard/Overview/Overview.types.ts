export interface Metric { label: string; count: number; color?: string }
export interface Barrier {
  id: number; name: string; lat: number | null; lng: number | null;
  status: string; status_label: string; blocks_map: boolean;
  category_name: string; color: string; url: string;
}
export interface OverviewProps {
  specializedEducationalSupport: {
    totalStudents: number; totalProfessionals: number; totalPeisFinished: number;
    totalPeisNotFinished: number; totalPeis: number; totalSessions: number;
    totalPendingPendencies: number; totalOverduePendencies: number;
    pendenciesByPriority: Metric[]; sessionsByStatus: Metric[];
  };
  inclusiveRadar: {
    totalAt: number; totalAem: number; totalLoans: number; totalWaiting: number;
    totalBarriers: number; barrierStatusCounts: Metric[]; mapBarriers: Barrier[];
  };
}
