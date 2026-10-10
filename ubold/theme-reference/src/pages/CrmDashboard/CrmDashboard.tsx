import DealStatCard from "@/Components/UI/DealStatCard/DealStatCard";
import CardBox from "@/Components/UI/CardBox/CardBox";
import ChartLegend from "@/Components/UI/ChartLegend/ChartLegend";
import MorrisChart from "@/Components/UI/MorrisChart/MorrisChart";
import Table from "@/Components/Tables/Table/Table";
import TableLabel from "@/Components/UI/TableLabel/TableLabel";
import RecordActions from "@/Components/UI/RecordActions/RecordActions";
import ScrollArea from "@/Components/Navigation/ScrollArea/ScrollArea";
import ActivityTimeline from "@/Components/UI/ActivityTimeline/ActivityTimeline";
import { profileActivity } from "@/Components/UI/ActivityTimeline/ActivityTimeline.data";
import {
  dealStats,
  dealLegend,
  groupLegend,
  dealLineOptions,
  dealBarOptions,
  groupDonutOptions,
  dashboardContacts,
} from "./CrmDashboard.data";
import "./CrmDashboard.css";
export default function CrmDashboard(): JSX.Element {
  return (
    <div className="crmDashboard">
      <div className="crmDashboard__row crmDashboard__stats">
        {dealStats.map((stat) => (
          <div key={stat.title}>
            <DealStatCard stat={stat} />
          </div>
        ))}
      </div>
      <CardBox>
        <h4 className="crmDashboard__title">Análise de negócios</h4>
        <ChartLegend items={dealLegend} />
        <MorrisChart
          type="line"
          height={300}
          options={dealLineOptions}
          label="Análise anual de negócios ganhos e perdidos"
        />
      </CardBox>
      <div className="crmDashboard__row crmDashboard__charts">
        <div>
          <CardBox>
            <h4 className="crmDashboard__title crmDashboard__title--spaced">
              Tempo médio por negócio
            </h4>
            <MorrisChart
              type="bar"
              height={320}
              options={dealBarOptions}
              label="Tempo médio por negócio"
            />
          </CardBox>
        </div>
        <div>
          <CardBox>
            <h4 className="crmDashboard__title crmDashboard__title--spaced">
              Vendas por grupo de produtos
            </h4>
            <ChartLegend items={groupLegend} />
            <MorrisChart
              type="donut"
              height={274}
              options={groupDonutOptions}
              label="Vendas por grupo de produtos"
            />
          </CardBox>
        </div>
      </div>
      <div className="crmDashboard__row crmDashboard__details">
        <div>
          <CardBox>
            <h4 className="crmDashboard__title">Contatos</h4>
            <p className="crmDashboard__description crmDashboard__description--contacts">
              Seu texto incrível vai aqui.
            </p>
            <ScrollArea height={300} label="Contatos do CRM">
              <div className="crmDashboard__tableScroll">
                <Table className="crmDashboard__contacts">
                  <thead>
                    <tr>
                      <th>Nome</th>
                      <th>Categoria</th>
                      <th>E-mail</th>
                      <th>Status</th>
                      <th>Ação</th>
                    </tr>
                  </thead>
                  <tbody>
                    {dashboardContacts.map((contact) => (
                      <tr key={contact.id}>
                        <td>{contact.name}</td>
                        <td>{contact.category}</td>
                        <td>{contact.email}</td>
                        <td>
                          <TableLabel>{contact.status}</TableLabel>
                        </td>
                        <td>
                          <RecordActions name={contact.name} />
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </Table>
              </div>
            </ScrollArea>
          </CardBox>
        </div>
        <aside>
          <CardBox>
            <h4 className="crmDashboard__title">Atividades</h4>
            <p className="crmDashboard__description">
              Seu texto incrível vai aqui.
            </p>
            <ScrollArea
              height={320}
              className="crmDashboard__activities"
              label="Atividades do CRM"
            >
              <ActivityTimeline items={profileActivity} compactTime />
            </ScrollArea>
          </CardBox>
        </aside>
      </div>
    </div>
  );
}
