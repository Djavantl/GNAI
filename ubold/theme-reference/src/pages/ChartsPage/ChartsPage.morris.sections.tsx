import Portlet from "@/Components/UI/Portlet/Portlet";
import MorrisChart from "@/Components/UI/MorrisChart/MorrisChart";
import { morrisExamples } from "./ChartsPage.morris.data";

export default function MorrisExamples(): JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-sm-6"}>
          <Portlet removeImmediately title={"Bar Chart"}>
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5fbeaa" }}
                    ></i>
                    {"Series A"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5d9cec" }}
                    ></i>
                    {"Series B"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#dcdcdc" }}
                    ></i>
                    {"Series C"}
                  </h5>
                </li>
              </ul>
            </div>
            <MorrisChart
              {...morrisExamples["morris-bar-example"]}
              height={300}
            />
          </Portlet>
        </div>
        <div className={"col-sm-6"}>
          <Portlet removeImmediately title={"Stacked Bar Chart"}>
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
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
                      style={{ color: "#dcdcdc" }}
                    ></i>
                    {"Series B"}
                  </h5>
                </li>
              </ul>
            </div>
            <MorrisChart
              {...morrisExamples["morris-bar-stacked"]}
              height={300}
            />
          </Portlet>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Area Chart with Point"}>
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#36404a" }}
                    ></i>
                    {"Series A"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5d9cec" }}
                    ></i>
                    {"Series B"}
                  </h5>
                </li>
              </ul>
            </div>
            <MorrisChart
              {...morrisExamples["morris-area-with-dotted"]}
              height={300}
            />
          </Portlet>
        </div>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Area Chart"}>
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5fbeaa" }}
                    ></i>
                    {"Series A"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5d9cec" }}
                    ></i>
                    {"Series B"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#bbbbbb" }}
                    ></i>
                    {"Series C"}
                  </h5>
                </li>
              </ul>
            </div>
            <MorrisChart
              {...morrisExamples["morris-area-example"]}
              height={300}
            />
          </Portlet>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Line Chart"}>
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#36404a" }}
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
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5d9cec" }}
                    ></i>
                    {"Series C"}
                  </h5>
                </li>
              </ul>
            </div>
            <MorrisChart
              {...morrisExamples["morris-line-example"]}
              height={300}
            />
          </Portlet>
        </div>
        <div className={"col-lg-6"}>
          <Portlet removeImmediately title={"Donut Chart"}>
            <MorrisChart
              {...morrisExamples["morris-donut-example"]}
              height={300}
            />
            <div className={"text-center"}>
              <ul className={"list-inline chart-detail-list"}>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5fbeaa" }}
                    ></i>
                    {"In-Store Sales"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#5d9cec" }}
                    ></i>
                    {"Mail-Order Sales"}
                  </h5>
                </li>
                <li>
                  <h5>
                    <i
                      className={"fa fa-circle m-r-5"}
                      style={{ color: "#dcdcdc" }}
                    ></i>
                    {"Download Sales"}
                  </h5>
                </li>
              </ul>
            </div>
          </Portlet>
        </div>
      </div>
    </>
  );
}
