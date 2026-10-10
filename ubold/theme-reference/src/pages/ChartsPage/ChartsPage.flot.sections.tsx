import Portlet from "@/Components/UI/Portlet/Portlet";
import FlotChart from "@/Components/UI/FlotChart/FlotChart";
import { flotExamples } from "./ChartsPage.flot.data";

export default function FlotExamples(): JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Multiple Statistics"}>
            <FlotChart {...flotExamples["#website-stats"]} />
          </Portlet>
        </div>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Realtime Statistics"}>
            <FlotChart {...flotExamples["#flotRealTime"]} realtime />
          </Portlet>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Donut Pie"}>
            <div>
              <FlotChart
                {...flotExamples["#donut-chart #donut-chart-container"]}
              />
            </div>
          </Portlet>
        </div>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Pie Chart"}>
            <div>
              <FlotChart {...flotExamples["#pie-chart #pie-chart-container"]} />
            </div>
          </Portlet>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-12"}>
          <Portlet removeImmediately title={"Pie Chart"}>
            <FlotChart {...flotExamples["#ordered-bars-chart"]} />
          </Portlet>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-12"}>
          <Portlet removeImmediately title={"Combine Statistics"}>
            <div>
              <FlotChart
                {...flotExamples["#combine-chart #combine-chart-container"]}
              />
            </div>
          </Portlet>
        </div>
      </div>
    </>
  );
}
