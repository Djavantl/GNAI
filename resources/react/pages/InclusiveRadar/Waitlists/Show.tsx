import { router } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import WaitlistPage from "./WaitlistPage";
import WaitlistResourceDetails from "./WaitlistResourceDetails";
import type { WaitlistRecord } from "./Waitlist.types";
import "./Waitlists.css";

type Props = {
    waitlist: WaitlistRecord;
    canCancel: boolean;
    statusLabel: string;
    statusColor: string;
    can: { edit: boolean; cancel: boolean; destroy: boolean; pdf: boolean };
};

const base = "/inclusive-radar/waitlists";

export default function InclusiveRadarWaitlistsShowPage({
    waitlist,
    canCancel,
    statusLabel,
    statusColor,
    can,
}: Props): React.JSX.Element {
    const [action, setAction] = useState<"cancel" | "delete" | null>(null);
    const [processing, setProcessing] = useState(false);
    const [failure, setFailure] = useState("");
    const url = `${base}/${waitlist.id}`;

    function closeModal(): void {
        if (processing) return;
        setAction(null);
        setFailure("");
    }

    function confirmAction(): void {
        if (processing || !action) return;
        setFailure("");
        setProcessing(true);
        if (action === "cancel") {
            router.patch(`${url}/cancel`, {}, {
                preserveScroll: true,
                onError: () => setFailure("Não foi possível cancelar a solicitação. Tente novamente."),
                onSuccess: () => setAction(null),
                onFinish: () => setProcessing(false),
            });
        } else {
            router.delete(url, {
                preserveScroll: true,
                onError: () => setFailure("Não foi possível excluir a solicitação. Tente novamente."),
                onSuccess: () => setAction(null),
                onFinish: () => setProcessing(false),
            });
        }
    }

    return (
        <WaitlistPage
            title="Detalhes da Fila de Espera"
            subtitle="Visualize as informações da solicitação, o status e o histórico do recurso."
            waitlistId={waitlist.id}
        >
            <CardBox className="waitlistShow__card">
                <FormSection
                    id="waitlist-show-resource"
                    title="Recurso Solicitado"
                    variant="inline"
                    iconClass={waitlist.waitlistable_type === "assistive_technology" ? "fa fa-microchip" : "fa fa-book"}
                >
                    <WaitlistResourceDetails {...waitlist} />
                </FormSection>
                <FormSection
                    id="waitlist-show-beneficiary"
                    title="Beneficiário e Usuário Responsável"
                    variant="inline"
                    iconClass="fa fa-users"
                >
                    <dl className="waitlistShow__grid">
                        <DetailField label="Estudante (Beneficiário)">
                            {waitlist.student?.name ?? "Não informado"}
                        </DetailField>
                        <DetailField label="Profissional (Beneficiário)">
                            {waitlist.professional?.name ?? "Não informado"}
                        </DetailField>
                        <DetailField label="Usuário Responsável">
                            {waitlist.user || "—"}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="waitlist-show-status"
                    title="Status e Datas"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="waitlistShow__grid">
                        <DetailField label="Data da Solicitação">
                            {waitlist.requested_at_label}
                        </DetailField>
                        <DetailField label="Status da Solicitação">
                            <TableLabel tone={statusColor}>{statusLabel}</TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="waitlist-show-observation"
                    title="Observações"
                    variant="inline"
                    iconClass="fa fa-comment"
                >
                    <dl className="waitlistShow__grid">
                        <DetailField label="Observações" wide>
                            {waitlist.observation_html ? (
                                <div
                                    className="waitlistShow__richText"
                                    dangerouslySetInnerHTML={{ __html: waitlist.observation_html }}
                                />
                            ) : "Nenhuma observação registrada."}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${url}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Exportar solicitação em PDF"
                        aria-label="Exportar solicitação em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para a fila de espera"
                    aria-label="Voltar para a fila de espera"
                    disabled={processing}
                />
                {can.edit ? (
                    <Button
                        href={`${url}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar solicitação"
                        aria-label="Editar solicitação"
                        disabled={processing}
                    />
                ) : null}
                {can.cancel && canCancel ? (
                    <Button
                        tone="danger"
                        leftIconClass="fa fa-ban"
                        label="Cancelar solicitação"
                        title="Cancelar solicitação"
                        aria-label="Cancelar solicitação"
                        disabled={processing}
                        onClick={() => setAction("cancel")}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir solicitação"
                        aria-label="Excluir solicitação"
                        disabled={processing}
                        onClick={() => setAction("delete")}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={action !== null}
                title={action === "cancel" ? "Cancelar Solicitação" : "Excluir Solicitação"}
                message={action === "cancel"
                    ? <>A solicitação <strong>#{waitlist.id}</strong> será cancelada e deixará de ficar ativa.</>
                    : <>A solicitação <strong>#{waitlist.id}</strong> será excluída permanentemente.</>}
                processing={processing}
                confirmLabel={action === "cancel" ? "Confirmar cancelamento" : "Confirmar exclusão"}
                processingLabel={action === "cancel" ? "Cancelando solicitação..." : "Excluindo solicitação..."}
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={closeModal}
                onConfirm={confirmAction}
            />
        </WaitlistPage>
    );
}
