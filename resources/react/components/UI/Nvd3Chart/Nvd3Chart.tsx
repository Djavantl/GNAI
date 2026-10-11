import { useCallback } from "react";
import { chartWindow } from "@/utils/chartRuntime";
import type { ChartMount } from "@/hooks/useChart.types";
import { useChart } from "@/hooks/useChart";
import "./Nvd3Chart.css";
export type Nvd3Factory = (svg: SVGSVGElement, nv: any, d3: any) => any;
const scripts = [
  "/assets/react/plugins/d3/d3.min.js",
  "/assets/react/plugins/nvd3/build/nv.d3.min.js",
];
export default function Nvd3Chart({
  create,
  height = 400,
}: {
  create: Nvd3Factory;
  height?: number;
}): React.JSX.Element {
  const mount = useCallback<ChartMount>(
    (element) => {
      const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.style.cssText = `height:${height}px;width:100%`;
      element.append(svg);
      const chart = create(svg, chartWindow.nv, chartWindow.d3);
      return {
        resize: () => chart.update(),
        destroy: () => {
          chartWindow.d3.select(svg).interrupt();
          chartWindow.d3.select(svg).selectAll("*").interrupt();
          for (const tooltip of [
            chart.tooltip,
            chart.interactiveLayer?.tooltip,
          ]) {
            tooltip?.hidden?.(true);
            const id = tooltip?.id?.();
            if (id) document.getElementById(id)?.remove();
          }
        },
      };
    },
    [create, height],
  );
  const ref = useChart(scripts, mount);
  return <div ref={ref} />;
}
