import SparklineChart from "@/components/UI/SparklineChart/SparklineChart";
import { sparklineExamples } from "./ChartsPage.sparkline.data";

export default function SparklineExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Line Chart"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline1"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Bar Chart"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline2"]}
              className={"text-center"}
            />
          </div>
        </div>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Pie Chart"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline3"]}
              className={"text-center"}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Custom Line Chart"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline4"]}
              className={"text-center"}
            />
          </div>
        </div>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Mouse Speed Chart Example"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline5"]}
              className={"text-center"}
            />
          </div>
        </div>
        <div className={"col-md-4"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Composite bar Chart"}</b>
            </h4>
            <SparklineChart
              chart={sparklineExamples["sparkline6"]}
              className={"text-center"}
            />
          </div>
        </div>
      </div>
    </>
  );
}
