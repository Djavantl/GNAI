import { otherAttributes } from "./ChartsPage.other.options.data";
import { otherOptions } from "./ChartsPage.other.options.data";
import KnobChart from "@/components/UI/KnobChart/KnobChart";
import EasyPieChart from "@/components/UI/EasyPieChart/EasyPieChart";
import CirclifulChart from "@/components/UI/CirclifulChart/CirclifulChart";

export default function OtherExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-sm-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Jquery Knob"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the button classes on an "}
              <code>{"<a>"}</code>
              {", "}
              <code>{"<button>"}</code>
              {", or "}
              <code>{"<input>"}</code>
              {" element. "}
            </p>
            <div className={"row"}>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={35} options={otherOptions[0]} />
                  <h5 className={"font-600 text-muted"}>
                    {"Disable display input"}
                  </h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={29} options={otherOptions[1]} />
                  <h5 className={"font-600 text-muted"}>{"Cursor mode"}</h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={44} options={otherOptions[2]} />
                  <h5 className={"font-600 text-muted"}>
                    {"Display previous value"}
                  </h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={44} options={otherOptions[3]} />
                  <h5 className={"font-600 text-muted"}>
                    {"Angle offset and arc"}
                  </h5>
                </div>
              </div>
            </div>
            <div className={"row"}>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={35} options={otherOptions[4]} />
                  <h5 className={"font-600 text-muted"}>{"Angle offset"}</h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={-11000} options={otherOptions[5]} />
                  <h5 className={"font-600 text-muted"}>
                    {"5-digit values, step 1000"}
                  </h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={80} options={otherOptions[6]} />
                  <h5 className={"font-600 text-muted"}>{"Readonly"}</h5>
                </div>
              </div>
              <div className={"col-sm-6 col-lg-3 text-center"}>
                <div className={"p-20"}>
                  <KnobChart value={44} options={otherOptions[7]} />
                  <h5 className={"font-600 text-muted"}>
                    {"Angle offset and arc"}
                  </h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-md-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Easy Pie Chart"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the button classes on an "}
              <code>{"<a>"}</code>
              {", "}
              <code>{"<button>"}</code>
              {", or "}
              <code>{"<input>"}</code>
              {" element. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-sm-6 col-lg-3"}>
                <EasyPieChart percent={86} options={otherOptions[8]} />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <EasyPieChart percent={86} options={otherOptions[9]} />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <EasyPieChart percent={86} options={otherOptions[10]} />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <EasyPieChart percent={56} options={otherOptions[11]} />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-md-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Circliful Charts"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the button classes on an "}
              <code>{"<a>"}</code>
              {", "}
              <code>{"<button>"}</code>
              {", or "}
              <code>{"<input>"}</code>
              {" element. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart m-b-30"}
                  attributes={otherAttributes[0]}
                />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart m-b-30"}
                  attributes={otherAttributes[1]}
                />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart m-b-30"}
                  attributes={otherAttributes[2]}
                />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart m-b-30"}
                  attributes={otherAttributes[3]}
                />
              </div>
            </div>
            <div className={"row text-center"}>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart"}
                  attributes={otherAttributes[4]}
                />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart"}
                  attributes={otherAttributes[5]}
                />
              </div>
              <div className={"col-sm-6 col-lg-3"}>
                <CirclifulChart
                  className={"circliful-chart"}
                  attributes={otherAttributes[6]}
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
