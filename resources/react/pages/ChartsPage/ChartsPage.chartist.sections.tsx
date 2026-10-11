import ChartistChart from "@/components/UI/ChartistChart/ChartistChart";
import { chartistExamples } from "./ChartsPage.chartist.data";

export default function ChartistExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Advanced Smil Animations"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#smil-animations"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Simple line chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#simple-line-chart"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Line Scatter Diagram"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#scatter-diagram"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Line chart with tooltips"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#line-chart-tooltips"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Line chart with area"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#chart-with-area"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Bi-polar Line chart with area only"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#bi-polar-line"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"SVG Path animation"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#svg-animation"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Line Interpolation / Smoothing"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#line-smoothing"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Different configuration for different series"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#different-series"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"SVG Animations chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Label placement chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#svg-dot-animation"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Bi-polar bar chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#bi-polar-bar"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Overlapping bars on mobile"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#overlapping-bars"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Multi-line labels"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#multi-line-chart"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Stacked bar chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#stacked-bar-chart"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Horizontal bar chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#horizontal-bar-chart"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Extreme responsive configuration"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#extreme-chart"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Distributed series"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#distributed-series"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Label placement"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Label placement chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#label-placement-chart"]}
              className={""}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Animating a Donut with Svg.animate"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#animating-donut"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Simple pie chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Label placement chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#simple-pie"]}
              className={"simple-pie-chart-chartist"}
            />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Pie chart with custom labels"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Horizontal bar chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#pie-chart"]}
              className={""}
            />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Gauge chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Example of Label placement chart. "}
            </p>
            <ChartistChart
              create={chartistExamples["#gauge-chart"]}
              className={""}
            />
          </div>
        </div>
      </div>
    </>
  );
}
