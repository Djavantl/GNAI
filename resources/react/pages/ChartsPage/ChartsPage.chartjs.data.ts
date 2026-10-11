import type { ChartJsChartProps } from "@/components/UI/ChartJsChart/ChartJsChart";
// Data and options extracted from the corresponding legacy chart demo.
export const chartjsExamples: Record<string, ChartJsChartProps> = {
  "#lineChart": {
    type: "Line",
    data: {
      labels: ["January", "February", "March", "April", "May", "June", "July"],
      datasets: [
        {
          fillColor: "rgba(93, 156, 236, 0.5)",
          strokeColor: "rgba(93, 156, 236, 1)",
          pointColor: "rgba(93, 156, 236, 1)",
          pointStrokeColor: "#fff",
          data: [33, 52, 63, 92, 50, 53, 46],
        },
        {
          fillColor: "rgba(95, 190, 170, 0.5)",
          strokeColor: "rgba(95, 190, 170, 1)",
          pointColor: "rgba(95, 190, 170, 1)",
          pointStrokeColor: "#fff",
          data: [22, 20, 30, 60, 29, 25, 12],
        },
      ],
    },
    options: {},
  },
  "#doughnut": {
    type: "Doughnut",
    data: [
      { value: 300, color: "#5fbeaa", highlight: "#5fbeaa", label: "Chrome" },
      { value: 50, color: "#36404a", highlight: "#36404a", label: "IE" },
      { value: 40, color: "#ebeff2", highlight: "#ebeff2", label: "Other" },
      { value: 120, color: "#5d9cec", highlight: "#5d9cec", label: "Firefox" },
    ],
    options: {},
  },
  "#pie": {
    type: "Pie",
    data: [
      { value: 40, color: "#ebeff2", label: "IE" },
      { value: 80, color: "#5fbeaa", label: "Chrome" },
      { value: 70, color: "#5d9cec", label: "Safari" },
    ],
    options: {},
  },
  "#bar": {
    type: "Bar",
    data: {
      labels: ["January", "February", "March", "April", "May", "June", "July"],
      datasets: [
        {
          fillColor: "rgba(93, 156, 236, 0.7)",
          strokeColor: "rgba(93, 156, 236, 1)",
          highlightFill: "rgba(93, 156, 236, 1)",
          highlightStroke: "rgba(93, 156, 236, 0.9)",
          data: [65, 59, 90, 81, 56, 55, 40],
        },
        {
          fillColor: "rgba(235, 239, 242, 0.7)",
          strokeColor: "rgba(235, 239, 242, 1)",
          highlightFill: "rgba(235, 239, 242, 1)",
          highlightStroke: "rgba(235, 239, 242, 0.9)",
          data: [28, 48, 40, 19, 96, 27, 100],
        },
      ],
    },
    options: {},
  },
  "#bar-single": {
    type: "Bar",
    data: {
      labels: [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ],
      datasets: [
        {
          fillColor: "#ebeff2",
          strokeColor: "#ebeff2",
          highlightFill: "#5fbeaa",
          highlightStroke: "#ebeff2",
          data: [65, 59, 90, 81, 56, 55, 40, 34, 50, 35, 90, 75],
        },
      ],
    },
    options: {},
  },
  "#radar": {
    type: "Radar",
    data: {
      labels: [
        "Eating",
        "Drinking",
        "Sleeping",
        "Designing",
        "Coding",
        "Partying",
        "Running",
      ],
      datasets: [
        {
          fillColor: "rgba(93, 156, 236, 0.5)",
          strokeColor: "rgba(93, 156, 236, 0.5)",
          pointColor: "rgba(93, 156, 236, 1)",
          pointStrokeColor: "#fff",
          data: [65, 59, 90, 81, 56, 55, 40],
        },
        {
          fillColor: "rgba(54, 64, 74, 0.5)",
          strokeColor: "rgba(54, 64, 74, 0.8)",
          pointColor: "rgba(54, 64, 74, 1)",
          pointStrokeColor: "#fff",
          data: [28, 48, 40, 19, 96, 27, 100],
        },
      ],
    },
    options: {},
  },
  "#polarArea": {
    type: "PolarArea",
    data: [
      { value: 90, color: "#5fbeaa", label: "Chrom" },
      { value: 24, color: "#1a2942", label: "Firefox" },
      { value: 58, color: "#5d9cec", label: "Other" },
      { value: 42, color: "#ebeff2", label: "IE9+" },
    ],
    options: {},
  },
};
