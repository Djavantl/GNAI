import RadialChart from "@/components/UI/RadialChart/RadialChart";

export default function RadialExamples(): React.JSX.Element {
  return (
    <>
      <div className={"row"}>
        <div className={"col-lg-12"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radial Variants"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Examples of radial Variants. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-md-3"}>
                <RadialChart
                  value={10}
                  size={"lg"}
                  tone={"default"}
                  label={"10%"}
                />
                <p>
                  <code>{".radial-bar-default"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart value={20} size={"lg"} label={"20%"} />
                <p>
                  <code>{".radial-bar"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={30}
                  size={"lg"}
                  tone={"success"}
                  label={"30%"}
                />
                <p>
                  <code>{".radial-bar-success"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={40}
                  size={"lg"}
                  tone={"info"}
                  label={"40%"}
                />
                <p>
                  <code>{".radial-bar-info"}</code>
                </p>
              </div>
            </div>
            <div className={"row text-center m-t-40"}>
              <div className={"col-md-3"}>
                <RadialChart
                  value={50}
                  size={"lg"}
                  tone={"danger"}
                  label={"50%"}
                />
                <p>
                  <code>{".radial-bar-danger"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={65}
                  size={"lg"}
                  tone={"warning"}
                  label={"65%"}
                />
                <p>
                  <code>{".radial-bar-warning"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={75}
                  size={"lg"}
                  tone={"inverse"}
                  label={"75%"}
                />
                <p>
                  <code>{".radial-bar-inverse"}</code>
                </p>
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={85}
                  size={"lg"}
                  tone={"pink"}
                  label={"85%"}
                />
                <p>
                  <code>{".radial-bar-pink"}</code>
                </p>
              </div>
            </div>
            <div className={"row text-center m-t-40"}>
              <div className={"col-md-3"}>
                <RadialChart
                  value={95}
                  size={"lg"}
                  tone={"purple"}
                  label={"95%"}
                />
                <p>
                  <code>{".radial-bar-purple"}</code>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radial Size"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the classes on an "}
              <code>{".radial-bar-lg"}</code>
              {","}
              <code>{".radial-bar-sm"}</code>
              {","}
              <code>{".radial-bar-xs"}</code>
              {" element. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-md-3"}>
                <RadialChart
                  value={85}
                  size={"lg"}
                  tone={"pink"}
                  label={"85%"}
                />
              </div>
              <div className={"col-md-3"}>
                <RadialChart value={45} label={"45%"} />
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={60}
                  size={"sm"}
                  tone={"success"}
                  label={"60%"}
                />
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={20}
                  size={"xs"}
                  tone={"inverse"}
                  label={"20%"}
                />
              </div>
            </div>
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radial with Images"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the classes on an "}
              <code>{".radial-bar-lg"}</code>
              {","}
              <code>{".radial-bar-sm"}</code>
              {","}
              <code>{".radial-bar-xs"}</code>
              {" element. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-md-3"}>
                <RadialChart
                  value={20}
                  tone={"danger"}
                  size={"lg"}
                  image={"/assets/react/images/users/avatar-1.jpg"}
                />
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={35}
                  tone={"warning"}
                  label={"35%"}
                  image={"/assets/react/images/users/avatar-2.jpg"}
                />
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={50}
                  tone={"info"}
                  size={"sm"}
                  label={"50%"}
                  image={"/assets/react/images/users/avatar-3.jpg"}
                />
              </div>
              <div className={"col-md-3"}>
                <RadialChart
                  value={100}
                  tone={"default"}
                  size={"xs"}
                  label={"100%"}
                  image={"/assets/react/images/users/avatar-4.jpg"}
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className={"row"}>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radial with Tooltips"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Use the code on an "}
              <code>
                {'data-label="...%" data-toggle="tooltip" data-title="...%"'}
              </code>
              {" element. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-md-3"}>
                <br />
                <RadialChart
                  value={85}
                  tone={"default"}
                  label={"85%"}
                  tooltip={"85%"}
                />
              </div>
              <div className={"col-md-3"}>
                <br />
                <RadialChart
                  value={45}
                  tone={"default"}
                  label={"45%"}
                  tooltip={"45%"}
                />
              </div>
              <div className={"col-md-3"}>
                <br />
                <RadialChart
                  value={60}
                  tone={"default"}
                  label={"60%"}
                  tooltip={"60%"}
                />
              </div>
              <div className={"col-md-3"}>
                <br />
                <RadialChart
                  value={20}
                  tone={"default"}
                  label={"20%"}
                  tooltip={"20%"}
                />
              </div>
            </div>
          </div>
        </div>
        <div className={"col-lg-6"}>
          <div className={"card-box"}>
            <h4 className={"m-t-0 header-title"}>
              <b>{"Radial applications"}</b>
            </h4>
            <p className={"text-muted m-b-30 font-13"}>
              {" Examples of radial applications. "}
            </p>
            <div className={"row text-center"}>
              <div className={"col-md-4"}>
                <RadialChart value={35} label={"35%"} />
                <p>
                  <small>{"Conversions"}</small>
                </p>
              </div>
              <div className={"col-md-4"}>
                <RadialChart value={45} label={"45%"} />
                <p>
                  <small>{"Recurrent"}</small>
                </p>
              </div>
              <div className={"col-md-4"}>
                <RadialChart value={20} label={"20%"} />
                <p>
                  <small>{"Uniques"}</small>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
