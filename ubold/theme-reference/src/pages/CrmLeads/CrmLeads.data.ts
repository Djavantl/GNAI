import type { MorrisBarChartOptions } from "@/Components/UI/MorrisChart/MorrisChart";
export const leadsChartOptions: MorrisBarChartOptions = {
  data: [
    { y: "Seg", a: 45, b: 180 },
    { y: "Ter", a: 75, b: 65 },
    { y: "Qua", a: 100, b: 90 },
    { y: "Qui", a: 75, b: 65 },
    { y: "Sex", a: 100, b: 90 },
    { y: "Sáb", a: 75, b: 65 },
    { y: "Dom", a: 50, b: 40 },
  ],
  xkey: "y",
  ykeys: ["a", "b"],
  labels: ["Série A", "Série B"],
  stacked: true,
  hideHover: "auto",
  gridLineColor: "#eeeeee",
  barColors: ["#5d9cec", "#ebeff2"],
};
