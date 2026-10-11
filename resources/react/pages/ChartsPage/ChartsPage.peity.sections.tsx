import { peityOptions } from "./ChartsPage.peity.options.data";
import PeityChart from "@/components/UI/PeityChart/PeityChart";

export default function PeityExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Pie Charts"}</b>
            </h4>
            <table className={"table"}>
              <thead>
                <tr>
                  <th>{"Graph"}</th>
                  <th>{"Code"}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"1/5"}
                      options={peityOptions[0]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">1/5</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"226/360"}
                      options={peityOptions[1]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">226/360</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"0.52/1.561"}
                      options={peityOptions[2]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">0.52/1.561</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"1,4"}
                      options={peityOptions[3]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">1,4</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"226,134"}
                      options={peityOptions[4]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">226,134</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"pie"}
                      values={"0.52,1.041"}
                      options={peityOptions[5]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-pie" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">0.52,1.041</span>'
                      }
                    </code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Donut Charts"}</b>
            </h4>
            <table className={"table"}>
              <thead>
                <tr>
                  <th>{"Graph"}</th>
                  <th>{"Code"}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"1/5"}
                      options={peityOptions[6]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">1/5</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"226/360"}
                      options={peityOptions[7]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">226/360</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"0.52/1.561"}
                      options={peityOptions[8]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">0.52/1.561</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"1,4"}
                      options={peityOptions[9]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">1/4</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"226,134"}
                      options={peityOptions[10]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">226,134</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"1,2,3,2,2"}
                      options={peityOptions[11]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-donut" data-colors="#5fbeaa,#ebeff2" data-width="30" data-height="30">1,2,3,2,2</span>'
                      }
                    </code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Line and Bars Charts"}</b>
            </h4>
            <table className={"table"}>
              <thead>
                <tr>
                  <th>{"Graph"}</th>
                  <th>{"Code"}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <PeityChart
                      type="line"
                      values={"5,3,9,6,5,9,7,3,5,2,5,3,9,6,5,9,7,3,5,2"}
                      options={peityOptions[12]}
                      updating
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span class="updating-chart">5,3,9,6,5,9,7,3,5,2</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"line"}
                      values={"5,3,9,6,5,9,7,3,5,2"}
                      options={peityOptions[13]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-line" data-fill="#5fbeaa" data-stroke="#169c81" data-width="100" data-height="40">5,3,9,6,5,9,7,3,5,2</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"line"}
                      values={"5,3,2,-1,-3,-2,2,3,5,2"}
                      options={peityOptions[14]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-line" data-fill="#5fbeaa" data-stroke="#169c81" data-width="100" data-height="100">5,3,2,-1,-3,-2,2,3,5,2</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"line"}
                      values={"0,-3,-6,-4,-5,-4,-7,-3,-5,-2"}
                      options={peityOptions[15]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-line" data-fill="#5fbeaa" data-stroke="#169c81" data-width="100" data-height="100">0,-3,-6,-4,-5,-4,-7,-3,-5,-2</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"bar"}
                      values={"5,3,9,6,5,9,7,3,5,2"}
                      options={peityOptions[16]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-bar" data-colors="#5fbeaa,#ebeff2" data-width="100" data-height="30">5,3,9,6,5,9,7,3,5,2</span>'
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"bar"}
                      values={"5,3,2,-1,-3,-2,2,3,5,2"}
                      options={peityOptions[17]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        '<span data-plugin="peity-bar" data-colors="#5fbeaa,#ebeff2" data-width="100" data-height="30">5,3,2,-1,-3,-2,2,3,5,2</span>'
                      }
                    </code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 m-b-30 header-title"}>
              <b>{"Data-attributes charts"}</b>
            </h4>
            <table className={"table"}>
              <thead>
                <tr>
                  <th>{"Graph"}</th>
                  <th>{"Code"}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"6/7"}
                      options={peityOptions[18]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        ' <span data-plugin="peity-donut-alt" data-peity=\'{ "fill": ["#4c5667", "#ebeff2"], "innerRadius": 24, "radius": 30 }\'">6/7</span> '
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"4/7"}
                      options={peityOptions[19]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        ' <span data-plugin="peity-donut-alt" data-peity=\'{ "fill": ["#5fbeaa", "#ebeff2"], "innerRadius": 18, "radius": 28 }\'">4/7</span> '
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"5/7"}
                      options={peityOptions[20]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        ' <span data-plugin="peity-donut-alt" data-peity=\'{ "fill": ["#5d9cec", "#ebeff2"], "innerRadius": 20, "radius": 24 }\'">5/7</span> '
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"6/7"}
                      options={peityOptions[21]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        ' <span data-plugin="peity-donut-alt" data-peity=\'{ "fill": ["#fb6d9d", "#ebeff2"], "innerRadius": 18, "radius": 20 }\'">6/7</span> '
                      }
                    </code>
                  </td>
                </tr>
                <tr>
                  <td>
                    <PeityChart
                      type={"donut"}
                      values={"5/7"}
                      options={peityOptions[22]}
                    />
                  </td>
                  <td>
                    <code>
                      {
                        ' <span data-plugin="peity-donut-alt" data-peity=\'{ "fill": ["#7266ba", "#ebeff2"], "innerRadius": 18, "radius": 20 }\'">5/7</span> '
                      }
                    </code>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </>
  );
}
