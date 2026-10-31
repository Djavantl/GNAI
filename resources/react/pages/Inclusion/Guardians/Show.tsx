import { router } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import {
    dateLabel,
    studentRecordUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import GuardianPage from "./GuardianPage";
import type { GuardianShowProps } from "./Guardian.types";

export default function InclusionGuardiansShowPage({
    guardian,
    student,
    display,
    can,
}: GuardianShowProps): React.JSX.Element {
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const person = guardian.person;
    const itemUrl = `${studentsBase}/${student.id}/guardians/${guardian.id}`;
    const relationship = display.relationshipLabel || "---";

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
                failure("Não foi possível excluir o responsável. Tente novamente.");
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
        <GuardianPage
            title="Dados do Responsável"
            subtitle={`Responsável por: ${student.person.name}`}
            current={person.name}
            student={student}
        >
            <CardBox className="guardianShow__panel">
                <FormSection
                    id="guardian-relationship"
                    title="Vínculo Familiar"
                    variant="inline"
                    iconClass="fa fa-users"
                >
                    <div className="guardianShow__profile">
                        <img
                            src={person.photo_url ?? "/images/default-user.jpg"}
                            alt={`Foto de ${person.name}`}
                            className="guardianShow__photo"
                        />
                        <p className="guardianShow__name">{person.name}</p>
                        <TableLabel tone="inverse">{relationship}</TableLabel>
                    </div>
                    <dl className="guardianShow__grid guardianShow__grid--two">
                        <DetailField label="Parentesco / Relação">
                            <strong>{relationship}</strong>
                        </DetailField>
                        <DetailField label="Estudante Vinculado">
                            {student.person.name}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="guardian-personal"
                    title="Informações Pessoais"
                    variant="inline"
                    iconClass="fa fa-user"
                >
                    <dl className="guardianShow__grid">
                        <DetailField label="Nome Completo">
                            <strong>{person.name}</strong>
                        </DetailField>
                        <DetailField label="Gênero">
                            {display.genderLabel || "Não informado"}
                        </DetailField>
                        <DetailField label="CPF / Documento">
                            {person.document || "Não informado"}
                        </DetailField>
                        <DetailField label="Data de Nascimento">
                            {person.birth_date ? dateLabel(person.birth_date) : "Não informado"}
                        </DetailField>
                        <DetailField label="Idade">
                            {display.age != null ? `${display.age} anos` : "Não informado"}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="guardian-contact"
                    title="Canais de Contato"
                    variant="inline"
                    iconClass="fa fa-address-book"
                >
                    <dl className="guardianShow__grid guardianShow__grid--two">
                        <DetailField label="Telefone / WhatsApp">
                            {person.phone || "Não informado"}
                        </DetailField>
                        <DetailField label="E-mail">
                            {person.email || "Não informado"}
                        </DetailField>
                        <DetailField label="Endereço Residencial" wide>
                            <span className="guardianShow__text">
                                {person.address || "Endereço não cadastrado."}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={studentRecordUrl(student.id, "guardians")}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para os responsáveis no prontuário do aluno"
                    aria-label="Voltar para os responsáveis no prontuário do aluno"
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar responsável"
                        aria-label="Editar responsável"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir responsável"
                        aria-label="Excluir responsável"
                        disabled={deleting}
                        onClick={() => setDeleteModalOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteModalOpen}
                title="Excluir Responsável"
                message={
                    <>
                        Deseja remover o responsável <strong>{person.name}</strong>? Esta ação não pode ser desfeita.
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo responsável..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!deleting) setDeleteModalOpen(false);
                }}
                onConfirm={remove}
            />
        </GuardianPage>
    );
}
