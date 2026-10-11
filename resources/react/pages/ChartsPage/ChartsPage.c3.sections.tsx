import C3Chart from "@/components/UI/C3Chart/C3Chart";
import { c3Examples } from "./ChartsPage.c3.data";

export default function C3Examples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Bar Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#chart"]} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Stacked Area Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#chart-stacked"]} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Roated Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#roated-chart"]} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Combine Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#combine-chart"]} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Donut Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#donut-chart"]} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Pie Chart"}</b>
            </h4>
            <C3Chart {...c3Examples["#pie-chart"]} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Scatter Plot"}</b>
            </h4>
            <C3Chart {...c3Examples["#scatter-plot"]} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Line Chart with Regions"}</b>
            </h4>
            <C3Chart {...c3Examples["#line-regions"]} />
          </div>
        </div>
      </div>
    </>
  );
}
