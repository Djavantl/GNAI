import CrmToolbar from "@/components/UI/CrmToolbar/CrmToolbar";
import OpportunityCard from "@/components/UI/OpportunityCard/OpportunityCard";
import CardBox from "@/components/UI/CardBox/CardBox";
import C3Chart from "@/components/UI/C3Chart/C3Chart";
import { opportunities } from "@/pages/Crm/Crm.data";
import { opportunityChartOptions } from "./CrmOpportunities.data";
import "./CrmOpportunities.css";
export default function CrmOpportunities(): React.JSX.Element {
  return (
    <>
      <CrmToolbar
        kind="opportunity"
        addLabel="Adicionar novo"
        searchLabel="Pesquisar oportunidades"
      />
      <div className="crmOpportunities">
        <div>
          {opportunities.map((record, index) => (
            <OpportunityCard
              key={record.id}
              record={record}
              last={index === opportunities.length - 1}
            />
          ))}
        </div>
        <aside>
          <CardBox>
            <h4 className="crmOpportunities__title">Gráfico de status</h4>
            <C3Chart
              options={opportunityChartOptions}
              label="Distribuição de oportunidades por status"
            />
          </CardBox>
        </aside>
      </div>
    </>
  );
}
