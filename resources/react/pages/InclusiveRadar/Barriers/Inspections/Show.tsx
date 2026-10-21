import CardBox from "@/components/UI/CardBox/CardBox";
import Button from "@/components/UI/Button/Button";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import BarrierPage from "../BarrierPage";
import type { BarrierInspection, BarrierRecord } from "../Barrier.types";
import "../Barriers.css";

const statusLabels: Record<string, string> = { identified: "Identificada", under_analysis: "Em Análise", in_progress: "Em Tratamento", resolved: "Resolvida", not_applicable: "Não Aplicável" };
const inspectionLabels: Record<string, string> = { initial: "Vistoria Inicial", periodic: "Vistoria Periódica", return: "Retorno de Empréstimo", maintenance: "Manutenção" };

function dateLabel(value: string): string {
    return value.slice(0, 10).split("-").reverse().join("/");
}

export default function InclusiveRadarBarrierInspectionShowPage({
    barrier,
    inspection,
    descriptionHtml,
}: {
    barrier: BarrierRecord;
    inspection: BarrierInspection;
    descriptionHtml: string;
}): React.JSX.Element {
    return (
        <BarrierPage
            title="Detalhes da Inspeção"
            subtitle="Visualize o status da barreira, parecer técnico e evidências coletadas."
            current="Detalhes da Inspeção"
            barrier={barrier}
        >
            <CardBox>
                <FormSection id="barrier-inspection-details" title="Informações Gerais" variant="inline" iconClass="fa fa-clipboard">
                    <dl className="barrierShow__details">
                        <div className="barrierShow__item"><dt>Status da Barreira</dt><dd><TableLabel tone={inspection.status === "resolved" ? "success" : inspection.status === "in_progress" ? "warning" : "inverse"}>{statusLabels[inspection.status ?? ""] ?? "Identificada"}</TableLabel></dd></div>
                        <div className="barrierShow__item"><dt>Tipo de Inspeção</dt><dd>{inspectionLabels[inspection.type ?? ""] ?? inspection.type ?? "—"}</dd></div>
                        <div className="barrierShow__item"><dt>Data da Inspeção</dt><dd>{dateLabel(inspection.inspection_date)}</dd></div>
                        <div className="barrierShow__item barrierShow__wide"><dt>Parecer Técnico / Descrição</dt><dd><div dangerouslySetInnerHTML={{ __html: descriptionHtml }} /></dd></div>
                    </dl>
                </FormSection>
                <FormSection id="barrier-inspection-evidence" title="Evidências" variant="inline" iconClass="fa fa-paperclip">
                    <div className="barrierShow__details">
                        {inspection.evidences?.length ? <div className="barrierShow__evidences barrierShow__wide">
                            {inspection.evidences.map((evidence) => {
                                const url = `/storage/${evidence.path.split("/").map(encodeURIComponent).join("/")}`;
                                return <a className="barrierShow__evidence" href={url} target="_blank" rel="noreferrer" key={evidence.id}>
                                    {evidence.mime_type?.startsWith("image/") ? <img src={url} alt={evidence.original_name ?? "Evidência da vistoria"} loading="lazy" /> : <i className="fa fa-file-text-o" aria-hidden="true" />}
                                    <span>{evidence.original_name ?? evidence.path.split("/").pop()}</span>
                                </a>;
                            })}
                        </div> : <p className="barrierShow__wide">Nenhuma evidência registrada para esta inspeção.</p>}
                    </div>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button href={`/inclusive-radar/barriers/${barrier.id}`} tone="cancel" label="Voltar" />
            </FormActions>
        </BarrierPage>
    );
}
