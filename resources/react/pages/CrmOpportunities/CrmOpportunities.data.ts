export const opportunityChartOptions: Record<string, unknown> = {
  data: {
    columns: [
      ["Quente", 46],
      ["Frio", 24],
      ["Em andamento", 46],
      ["Perdido", 10],
      ["Ganho", 30],
    ],
    type: "pie",
  },
  color: { pattern: ["#34d3eb", "#7266ba", "#ffbd4a", "#f05050", "#81c868"] },
  pie: { label: { show: false } },
  tooltip: {
    format: {
      value: (_value: number, ratio: number): string =>
        `${(ratio * 100).toFixed(1).replace(".", ",")}%`,
    },
  },
};
