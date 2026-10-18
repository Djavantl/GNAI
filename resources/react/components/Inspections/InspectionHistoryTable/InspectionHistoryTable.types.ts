export interface InspectionHistoryItem {
  id: number;
  date: string;
  type: string;
  state: string;
  descriptionHtml: string | null;
  evidenceCount: number;
}
