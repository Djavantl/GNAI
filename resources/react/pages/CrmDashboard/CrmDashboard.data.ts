import type { DealStat } from "@/components/UI/DealStatCard/DealStatCard.types";
import type { ChartLegendItem } from "@/components/UI/ChartLegend/ChartLegend";
export const dealStats: DealStat[] = [
  {
    title: "Ativos",
    count: 120,
    icon: "icon-layers",
    color: "#34d3eb",
    chart: "bar",
    values: "5,3,9,6,5,9,7,3,5,2,9,7,2,1",
    options: { fill: ["#34d3eb", "#ebeff2"], width: 120, height: 45 },
  },
  {
    title: "Ganhos",
    count: 95,
    icon: "icon-trophy",
    color: "#5fbeaa",
    chart: "pie",
    values: "1/5",
    options: { fill: ["#5fbeaa", "#ebeff2"], width: 50, height: 45 },
  },
  {
    title: "Perdidos",
    count: 85,
    icon: "icon-close",
    color: "#f05050",
    chart: "donut",
    values: "1/5",
    options: { fill: ["#f05050", "#ebeff2"], width: 50, height: 45 },
  },
];
export const dealLegend: ChartLegendItem[] = [
  { label: "Negócio — Ganho", color: "#81c868" },
  { label: "Negócio — Perdido", color: "#ffbd4a" },
];
export const groupLegend: ChartLegendItem[] = [
  { label: "Grupo 1", color: "#ebeff2" },
  { label: "Grupo 2", color: "#5fbeaa" },
  { label: "Grupo 3", color: "#5d9cec" },
];
export const dealLineOptions: Record<string, unknown> = {
  data: [
    { y: "2007", a: 20, b: 10 },
    { y: "2008", a: 35, b: 25 },
    { y: "2009", a: 40, b: 30 },
    { y: "2010", a: 55, b: 45 },
    { y: "2011", a: 60, b: 50 },
    { y: "2012", a: 75, b: 65 },
    { y: "2013", a: 80, b: 70 },
    { y: "2014", a: 95, b: 85 },
    { y: "2015", a: 100, b: 90 },
  ],
  xkey: "y",
  ykeys: ["a", "b"],
  labels: ["Negócio — Ganho ", "Negócio — Perdido "],
  fillOpacity: ["0.1"],
  pointFillColors: ["#ffffff"],
  pointStrokeColors: ["#999999"],
  behaveLikeLine: true,
  gridLineColor: "#eef0f2",
  hideHover: "auto",
  lineColors: ["#81c868", "#ffbd4a"],
};
export const dealBarOptions: Record<string, unknown> = {
  data: [
    { y: "2009", a: 100, b: 90 },
    { y: "2010", a: 75, b: 65 },
    { y: "2011", a: 50, b: 40 },
    { y: "2012", a: 75, b: 65 },
    { y: "2013", a: 50, b: 40 },
    { y: "2014", a: 75, b: 65 },
    { y: "2015", a: 100, b: 90 },
  ],
  xkey: "y",
  ykeys: ["a", "b"],
  labels: ["Negócios ganhos ", "Negócios perdidos "],
  hideHover: "auto",
  gridLineColor: "#eeeeee",
  barColors: ["#5fbeaa", "#5d9cec"],
};
export const groupDonutOptions: Record<string, unknown> = {
  data: [
    { label: "Grupo 1", value: 12 },
    { label: "Grupo 2", value: 30 },
    { label: "Grupo 3", value: 20 },
  ],
  colors: ["#ebeff2", "#5fbeaa", "#5d9cec"],
};
export const dashboardContacts = Array.from({ length: 7 }, (_, index) => ({
  id: `contact-${index}`,
  name: "John deo",
  category: "Categoria um",
  email: "johndeo@dummy.com",
  status: "Entregue",
}));
