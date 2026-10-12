import { Link } from "@inertiajs/react";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TableLabel from "@/components/UI/TableLabel/TableLabel";

export interface InspectionDetail {
    id: number;
    date: string;
    type: string;
    state: string;
    descriptionHtml: string | null;
    evidenceCount: number;
}

export default function InspectionHistory({
    technologyId,
    inspections,
}: {
    technologyId: number;
    inspections: InspectionDetail[];
}): React.JSX.Element {
    return (
        <FormSection
            id="technology-inspections"
            title="Histórico de Vistorias"
            variant="inline"
            iconClass="fa fa-history"
        >
            {inspections.length ? (
                <ol className="technologyShow__history">
                    {inspections.map((inspection) => (
                        <li key={inspection.id}>
                            <article className="technologyShow__inspection">
                                <div className="technologyShow__inspectionHeading">
                                    <TableLabel tone="purple">
                                        {inspection.date}
                                    </TableLabel>
                                    <span>{inspection.type}</span>
                                </div>
                                <dl className="technologyShow__inspectionFacts">
                                    <div>
                                        <dt>Estado de conservação</dt>
                                        <dd>{inspection.state}</dd>
                                    </div>
                                    <div>
                                        <dt>Evidências</dt>
                                        <dd>
                                            {inspection.evidenceCount
                                                ? `${inspection.evidenceCount} registrada(s)`
                                                : "Nenhuma evidência registrada"}
                                        </dd>
                                    </div>
                                </dl>
                                {inspection.descriptionHtml && (
                                    <div
                                        className="technologyShow__rich"
                                        dangerouslySetInnerHTML={{
                                            __html: inspection.descriptionHtml,
                                        }}
                                    />
                                )}
                                <Link
                                    className="technologyShow__inspectionLink"
                                    href={`/inclusive-radar/assistive-technologies/${technologyId}/inspection/${inspection.id}`}
                                    aria-label={`Ver detalhes da vistoria de ${inspection.date}`}
                                >
                                    Ver detalhes{" "}
                                    <i
                                        className="fa fa-angle-right"
                                        aria-hidden="true"
                                    />
                                </Link>
                            </article>
                        </li>
                    ))}
                </ol>
            ) : (
                <p className="technologyShow__empty">
                    Nenhum histórico de vistoria encontrado.
                </p>
            )}
        </FormSection>
    );
}
