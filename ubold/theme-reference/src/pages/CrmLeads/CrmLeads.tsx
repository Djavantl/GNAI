import CrmToolbar from "@/Components/UI/CrmToolbar/CrmToolbar";
import LeadCard from "@/Components/UI/LeadCard/LeadCard";
import CardBox from "@/Components/UI/CardBox/CardBox";
import MorrisChart from "@/Components/UI/MorrisChart/MorrisChart";
import ScrollArea from "@/Components/Navigation/ScrollArea/ScrollArea";
import ActivityTimeline from "@/Components/UI/ActivityTimeline/ActivityTimeline";
import { leads } from "@/pages/Crm/Crm.data";
import { crmActivity } from "@/Components/UI/ContactActivityCard/ContactActivityCard.data";
import { leadsChartOptions } from "./CrmLeads.data";
import "./CrmLeads.css";
export default function CrmLeads(): JSX.Element {
  return (
    <>
      <CrmToolbar
        kind="lead"
        addLabel="Adicionar novo"
        modalTitle="Adicionar leads"
        searchLabel="Pesquisar leads"
      />
      <div className="crmLeads">
        <div>
          {leads.map((record) => (
            <LeadCard key={record.id} record={record} />
          ))}
        </div>
        <aside>
          <CardBox className="crmLeads__statistics">
            <h4 className="crmLeads__title">Estatísticas de leads</h4>
            <MorrisChart
              type="bar"
              options={leadsChartOptions}
              height={260}
              label="Estatísticas de leads por dia da semana"
            />
            <div className="crmLeads__activity">
              <h4 className="crmLeads__title">
                <b>Atividades</b>
              </h4>
              <ScrollArea
                maxHeight={535}
                className="crmLeads__scroll"
                label="Atividades dos leads"
              >
                <ActivityTimeline items={crmActivity} compactTime />
              </ScrollArea>
            </div>
          </CardBox>
        </aside>
      </div>
    </>
  );
}
