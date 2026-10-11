import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import FlotExamples from "./ChartsPage.flot.sections";
import MorrisExamples from "./ChartsPage.morris.sections";
import ChartjsExamples from "./ChartsPage.chartjs.sections";
import C3Examples from "./ChartsPage.c3.sections";
import ChartistExamples from "./ChartsPage.chartist.sections";
import Nvd3Examples from "./ChartsPage.nvd3.sections";
import SparklineExamples from "./ChartsPage.sparkline.sections";
import PeityExamples from "./ChartsPage.peity.sections";
import RadialExamples from "./ChartsPage.radial.sections";
import OtherExamples from "./ChartsPage.other.sections";
import "./ChartsPage.css";
const pages = {
  flot: ["Flot Charts", FlotExamples],
  morris: ["Morris Chart", MorrisExamples],
  chartjs: ["Chartjs", ChartjsExamples],
  c3: ["C3 Charts", C3Examples],
  chartist: ["Chartist Charts", ChartistExamples],
  nvd3: ["Nvd3 Charts", Nvd3Examples],
  sparkline: ["Sparkline Charts", SparklineExamples],
  peity: ["Peity Charts", PeityExamples],
  radial: ["Radial charts", RadialExamples],
  other: ["Other Charts", OtherExamples],
} as const;
export default function ChartsPage({
  variant,
}: {
  variant: string;
}): React.JSX.Element {
  const [title, Examples] = pages[variant as keyof typeof pages];
  return (
    <section className={`chartsPage chartsPage--${variant}`}>
      <h4 className="chartsPage__title">{title}</h4>
      <PageBreadcrumb
        items={[
          { label: "Interface", href: "#" },
          { label: "Charts", href: "#" },
          { label: title },
        ]}
      />
      <Examples />
    </section>
  );
}
