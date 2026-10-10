import Nvd3Chart from "@/Components/UI/Nvd3Chart/Nvd3Chart";
import { nvd3Examples } from "./ChartsPage.nvd3.data";

export default function Nvd3Examples(): JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Line Chart"}</b>
            </h4>
            <div className={"line-chart"}>
              <Nvd3Chart
                create={nvd3Examples[".line-chart svg"]}
                height={400}
              />
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Discrete Bar Chart"}</b>
            </h4>
            <div className={"bar-chart"}>
              <Nvd3Chart create={nvd3Examples[".bar-chart svg"]} height={400} />
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Horizontal Grouped / Stacked Bar Chart"}</b>
            </h4>
            <div className={"multi-chart"}>
              <Nvd3Chart
                create={nvd3Examples[".multi-chart svg"]}
                height={500}
              />
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Scatter chart"}</b>
            </h4>
            <div className={"scatter-chart"}>
              <Nvd3Chart
                create={nvd3Examples[".scatter-chart svg"]}
                height={500}
              />
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-20 header-title"}>
              <b>{"Pie Chart & Donut Chart"}</b>
            </h4>
            <div className={"row"}>
              <div className={"col-md-6"}>
                <div>
                  <Nvd3Chart
                    create={nvd3Examples["#chart1 svg"]}
                    height={500}
                  />
                </div>
              </div>
              <div className={"col-md-6"}>
                <div>
                  <Nvd3Chart
                    create={nvd3Examples["#chart2 svg"]}
                    height={500}
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
