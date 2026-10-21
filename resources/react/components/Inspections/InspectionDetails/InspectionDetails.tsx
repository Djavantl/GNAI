import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import InspectionEvidenceCarousel from "@/components/Inspections/InspectionEvidenceCarousel/InspectionEvidenceCarousel";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type {
    InspectionDetailsCollection,
    InspectionDetailsData,
    InspectionDetailsSubject,
} from "./InspectionDetails.types";
import "./InspectionDetails.css";

export default function InspectionDetails({
    collection,
    inspection,
    subject,
}: {
    collection: InspectionDetailsCollection;
    inspection: InspectionDetailsData;
    subject: InspectionDetailsSubject;
}): React.JSX.Element {
    return (
        <BusinessPage
            title="Detalhes da Inspeção"
            subtitle="Visualize o estado de conservação, o tipo, o parecer técnico e as evidências da vistoria."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                collection,
                { label: subject.name, href: subject.href },
                { label: "Detalhes da Inspeção" },
            ]}
            actions={
                <div className="inspectionDetails__date">
                    <span>Data da inspeção</span>
                    <TableLabel tone="purple">{inspection.date}</TableLabel>
                </div>
            }
            className="inspectionDetails"
        >
            <CardBox className="inspectionDetails__panel">
                <FormSection
                    id="inspection-general"
                    title="Informações Gerais"
                    variant="inline"
                    iconClass="fa fa-search"
                >
                    <dl className="inspectionDetails__grid">
                        <DetailField label="Estado de Conservação">
                            {inspection.stateLabel}
                        </DetailField>
                        <DetailField label="Tipo de Inspeção">
                            {inspection.typeLabel}
                        </DetailField>
                        <DetailField label="Parecer Técnico / Descrição" wide>
                            <ExpandableContent>
                                <div
                                    className="inspectionDetails__rich"
                                    dangerouslySetInnerHTML={{
                                        __html: inspection.descriptionHtml,
                                    }}
                                />
                            </ExpandableContent>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="inspection-evidences"
                    title="Evidências"
                    variant="inline"
                    iconClass="fa fa-paperclip"
                >
                    {inspection.evidences.length ? (
                        <InspectionEvidenceCarousel
                            evidences={inspection.evidences}
                        />
                    ) : (
                        <div className="inspectionDetails__empty">
                            <i
                                className="fa fa-paperclip"
                                aria-hidden="true"
                            />
                            <p>
                                Nenhuma evidência registrada para esta
                                inspeção.
                            </p>
                        </div>
                    )}
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={subject.href}
                    tone="cancel"
                    label="Voltar"
                    title={`Voltar para ${subject.name}`}
                    aria-label={`Voltar para ${subject.name}`}
                />
            </FormActions>
        </BusinessPage>
    );
}
