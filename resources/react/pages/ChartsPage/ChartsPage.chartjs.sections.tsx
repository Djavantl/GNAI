import ChartJsChart from "@/components/UI/ChartJsChart/ChartJsChart";
import { chartjsExamples } from "./ChartsPage.chartjs.data";

export default function ChartjsExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Line Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "A line chart is a way of plotting data points on a line. Often, it is used to show trend data, and the comparison of two data sets. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Series A"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5fbeaa" }}
                  ></i>
                  {"Series B"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#lineChart"]} height={300} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Bar Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "A bar chart is a way of showing data as bars. It is sometimes used to show trend data, and the comparison of multiple data sets side by side. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Series A"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#ebeff2" }}
                  ></i>
                  {"Series B"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#bar"]} height={300} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Pie Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "Pie and doughnut charts are probably the most commonly used chart there are. They are divided into segments, the arc of each segment shows the proportional value of each piece of data. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Safari"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5fbeaa" }}
                  ></i>
                  {"Chrome"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#ebeff2" }}
                  ></i>
                  {"IE"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#pie"]} height={260} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Donut Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "Pie and doughnut charts are probably the most commonly used chart there are. They are divided into segments, the arc of each segment shows the proportional value of each piece of data. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#36404a" }}
                  ></i>
                  {"IE"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#ebeff2" }}
                  ></i>
                  {"Other"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Firefox"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5fbeaa" }}
                  ></i>
                  {"Chrome"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#doughnut"]} height={260} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Polar area Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "Polar area charts are similar to pie charts, but each segment has the same angle - the radius of the segment differs depending on the value. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#36404a" }}
                  ></i>
                  {"Firefox"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#ebeff2" }}
                  ></i>
                  {"IE"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Other"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5fbeaa" }}
                  ></i>
                  {"Chrome"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#polarArea"]} height={300} />
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radar Chart"}</b>
            </h4>
            <p className={"text-muted m-b-15 font-13"}>
              {
                "A radar chart is a way of showing multiple data points and the variation between them. They are often useful for comparing the points of two or more different data sets. "
              }
            </p>
            <ul className={"list-inline chart-detail-list text-center"}>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#5d9cec" }}
                  ></i>
                  {"Series A"}
                </h5>
              </li>
              <li>
                <h5>
                  <i
                    className={"fa fa-circle m-r-5"}
                    style={{ color: "#36404a" }}
                  ></i>
                  {"Series B"}
                </h5>
              </li>
            </ul>
            <ChartJsChart {...chartjsExamples["#radar"]} height={300} />
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Single Bar Chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {
                "A bar chart is a way of showing data as bars. It is sometimes used to show trend data, and the comparison of multiple data sets side by side. "
              }
            </p>
            <ChartJsChart {...chartjsExamples["#bar-single"]} height={300} />
          </div>
        </div>
      </div>
    </>
  );
}
