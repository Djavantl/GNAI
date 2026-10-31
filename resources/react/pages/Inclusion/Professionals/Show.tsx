import { router, usePage } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImpersonationAction from "@/components/Navigation/ImpersonationAction/ImpersonationAction";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { dateLabel } from "@/components/Students/Students.utils";
import ProfessionalPage from "./ProfessionalPage";
import { professionalsBase } from "./Professionals.utils";
import type { ProfessionalShowProps } from "./Professional.types";

export default function InclusionProfessionalsShowPage({
    professional,
    display,
    impersonationUserId,
    can,
}: ProfessionalShowProps): React.JSX.Element {
    const viewerIsAdmin = usePage<{
        auth?: { user?: { is_admin?: boolean } | null };
    }>().props.auth?.user?.is_admin === true;
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [impersonating, setImpersonating] = useState(false);
    const busy = deleting || impersonating;
    const person = professional.person;
    const itemUrl = `${professionalsBase}/${professional.id}`;

    function failure(text: string): void {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
    }

    function remove(): void {
        if (deleting) return;

        setDeleting(true);
        router.delete(itemUrl, {
            onHttpException: () => {
                failure("Não foi possível excluir o profissional. Tente novamente.");
                return false;
            },
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteModalOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <ProfessionalPage
            title="Perfil do Profissional"
            subtitle="Visualize os dados pessoais e funcionais do profissional ou tutor de pares. Embora esta seção esteja sob o título “Profissionais”, os tutores de pares também são exibidos e gerenciados aqui."
            current={person.name}
        >
            <CardBox className="professionalShow__panel">
                <FormSection
                    id="professional-identification"
                    title="Identificação Pessoal"
                    variant="inline"
                    iconClass="fa fa-user"
                >
                    <div className="professionalShow__profile">
                        <img
                            src={person.photo_url ?? "/images/default-user.jpg"}
                            alt={`Foto de ${person.name}`}
                            className="professionalShow__photo"
                        />
                        <p className="professionalShow__name">{person.name}</p>
                    </div>
                    <dl className="professionalShow__grid">
                        <DetailField label="Nome Completo">
                            <strong>{person.name}</strong>
                        </DetailField>
                        <DetailField label="CPF">
                            {person.document || "---"}
                        </DetailField>
                        <DetailField label="Data de Nascimento">
                            {dateLabel(person.birth_date)}
                        </DetailField>
                        <DetailField label="E-mail">
                            {person.email || "---"}
                        </DetailField>
                        <DetailField label="Telefone">
                            {person.phone || "---"}
                        </DetailField>
                        <DetailField label="Endereço" wide>
                            <span className="professionalShow__text">
                                {person.address || "---"}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="professional-employment"
                    title="Vínculo Profissional"
                    variant="inline"
                    iconClass="fa fa-briefcase"
                >
                    <dl className="professionalShow__grid">
                        <DetailField label="Cargo / Função">
                            <strong>
                                {professional.position?.name ?? "Não definido"}
                            </strong>
                        </DetailField>
                        <DetailField label="Matrícula">
                            {professional.registration || "---"}
                        </DetailField>
                        <DetailField label="Status">
                            {display.statusLabel ? (
                                <TableLabel tone={display.statusColor ?? "inverse"}>
                                    {display.statusLabel}
                                </TableLabel>
                            ) : (
                                "---"
                            )}
                        </DetailField>
                        <DetailField label="Aparece no site">
                            {professional.show_on_website ? "Sim" : "Não"}
                        </DetailField>
                        <DetailField label="Data de Admissão">
                            {dateLabel(professional.entry_date)}
                        </DetailField>
                        <DetailField label="Tempo de Instituição">
                            {display.serviceTime || "---"}
                        </DetailField>
                        {viewerIsAdmin ? (
                            <DetailField label="Administrador do Sistema">
                                {professional.user?.is_admin ? (
                                    <TableLabel tone="success">
                                        <i
                                            className="fa fa-shield"
                                            aria-hidden="true"
                                        />{" "}
                                        Sim
                                    </TableLabel>
                                ) : (
                                    <TableLabel tone="inverse">Não</TableLabel>
                                )}
                            </DetailField>
                        ) : null}
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={impersonationUserId !== null ? (
                    <ImpersonationAction
                        userId={impersonationUserId}
                        name={person.name}
                        disabled={deleting}
                        onProcessingChange={setImpersonating}
                    />
                ) : undefined}
            >
                <Button
                    href={professionalsBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para profissionais"
                    aria-label="Voltar para profissionais"
                    disabled={busy}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar profissional"
                        aria-label="Editar profissional"
                        disabled={busy}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir profissional"
                        aria-label="Excluir profissional"
                        disabled={busy}
                        onClick={() => setDeleteModalOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteModalOpen}
                title="Excluir Profissional"
                message={
                    <>
                        Excluir o profissional <strong>{person.name}</strong> do sistema?
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo profissional..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!deleting) setDeleteModalOpen(false);
                }}
                onConfirm={remove}
            />
        </ProfessionalPage>
    );
}
