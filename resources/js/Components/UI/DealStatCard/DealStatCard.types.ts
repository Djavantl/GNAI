export interface DealStat {
  title: string;
  count: number;
  icon: string;
  color: string;
  chart: "bar" | "pie" | "donut";
  values: string;
  options: Record<string, unknown>;
}
