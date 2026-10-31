import { router } from "@inertiajs/react";
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
import TeacherPage from "./TeacherPage";
import { teachersBase } from "./Teachers.utils";
import type { TeacherCourseDiscipline, TeacherShowProps } from "./Teacher.types";

type PendingAction = "delete" | "impersonate" | null;

function groupByCourse(
    items: TeacherCourseDiscipline[],
): Array<{ course: string; items: TeacherCourseDiscipline[] }> {
    const groups = new Map<string, TeacherCourseDiscipline[]>();

    items.forEach((item) => {
        const course = item.course?.name ?? "";
        groups.set(course, [...(groups.get(course) ?? []), item]);
    });

    return Array.from(groups, ([course, groupItems]) => ({
        course,
        items: groupItems,
    }));
}

export default function InclusionTeachersShowPage({
    teacher,
    display,
    impersonationUserId,
    can,
}: TeacherShowProps): React.JSX.Element {
    const [modal, setModal] = useState<PendingAction>(null);
    const [processing, setProcessing] = useState<PendingAction>(null);
    const person = teacher.person;
    const itemUrl = `${teachersBase}/${teacher.id}`;
    const courses = groupByCourse(teacher.course_disciplines ?? []);
    const busy = processing !== null;

    function failure(text: string): void {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
    }

    function requestOptions(httpMessage: string) {
        return {
            onHttpException: () => {
                failure(httpMessage);
                return false;
            },
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onSuccess: () => setModal(null),
            onFinish: () => setProcessing(null),
        };
    }

    function remove(): void {
        if (busy) return;

        setProcessing("delete");
        router.delete(
            itemUrl,
            requestOptions("Não foi possível excluir o professor. Tente novamente."),
        );
    }

    return (
        <TeacherPage
            title="Perfil do Professor"
            subtitle="Informações detalhadas do docente e atribuições acadêmicas."
            current={person.name}
        >
            <CardBox className="teacherShow__panel">
                <FormSection
                    id="teacher-identification"
                    title="Identificação Pessoal"
                    variant="inline"
                    iconClass="fa fa-user"
                >
                    <div className="teacherShow__profile">
                        <img
                            src={person.photo_url ?? "/images/default-user.jpg"}
                            alt={`Foto de ${person.name}`}
                            className="teacherShow__photo"
                        />
                        <p className="teacherShow__name">{person.name}</p>
                        <p className="teacherShow__role">Professor(a) / Docente</p>
                    </div>
                    <dl className="teacherShow__grid">
                        <DetailField label="Nome Completo">
                            <strong>{person.name}</strong>
                        </DetailField>
                        <DetailField label="CPF / Documento">
                            {person.document || "---"}
                        </DetailField>
                        <DetailField label="Data de Nascimento">
                            {dateLabel(person.birth_date)}
                        </DetailField>
                        <DetailField label="E-mail de Contato">
                            {person.email || "---"}
                        </DetailField>
                        <DetailField label="Telefone">
                            {person.phone || "---"}
                        </DetailField>
                        <DetailField label="Endereço" wide>
                            <span className="teacherShow__text">
                                {person.address || "---"}
                            </span>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="teacher-assignment"
                    title="Vínculo Docente"
                    variant="inline"
                    iconClass="fa fa-graduation-cap"
                >
                    <dl className="teacherShow__grid">
                        <DetailField label="Matrícula">
                            <code className="teacherShow__registration">
                                {teacher.registration}
                            </code>
                        </DetailField>
                        <DetailField label="Data de Cadastro">
                            {display.createdAt || "---"}
                        </DetailField>
                    </dl>
                    <h3 className="teacherShow__subheading">Disciplinas atribuídas</h3>
                    {courses.length ? (
                        <ul className="teacherShow__courses">
                            {courses.map((group) => (
                                <li
                                    key={group.course}
                                    className="teacherShow__course"
                                >
                                    <div className="teacherShow__courseHeader">
                                        <strong className="teacherShow__courseName">
                                            <i
                                                className="fa fa-graduation-cap"
                                                aria-hidden="true"
                                            />{" "}
                                            {group.course || "Curso não identificado"}
                                        </strong>
                                        <TableLabel tone="inverse">
                                            {group.items.length}{" "}
                                            {group.items.length === 1
                                                ? "disciplina"
                                                : "disciplinas"}
                                        </TableLabel>
                                    </div>
                                    <ul
                                        className="teacherShow__disciplines"
                                        aria-label={`Disciplinas em ${group.course || "curso não identificado"}`}
                                    >
                                        {group.items.map((assignment) => (
                                            <li key={assignment.id}>
                                                <i
                                                    className="fa fa-book"
                                                    aria-hidden="true"
                                                />{" "}
                                                {assignment.discipline?.name ?? "Disciplina não identificada"}
                                            </li>
                                        ))}
                                    </ul>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="teacherShow__empty">
                            <i
                                className="fa fa-book"
                                aria-hidden="true"
                            />
                            <strong>Nenhuma disciplina atribuída</strong>
                            <span>
                                Utilize o botão <strong>Gerenciar Disciplinas</strong> para realizar as atribuições.
                            </span>
                        </div>
                    )}
                </FormSection>
            </CardBox>
            <FormActions
                secondary={impersonationUserId !== null ? (
                    <ImpersonationAction
                        userId={impersonationUserId}
                        name={person.name}
                        disabled={busy}
                        onProcessingChange={(value) =>
                            setProcessing(value ? "impersonate" : null)
                        }
                    />
                ) : undefined}
            >
                <Button
                    href={teachersBase}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para professores"
                    aria-label="Voltar para professores"
                    disabled={busy}
                />
                {can.update ? (
                    <Button
                        href={`${itemUrl}/disciplines`}
                        tone="info"
                        leftIconClass="fa fa-book"
                        label="Gerenciar Disciplinas"
                        title="Gerenciar disciplinas do professor"
                        aria-label="Gerenciar disciplinas do professor"
                        disabled={busy}
                    />
                ) : null}
                {can.update ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar perfil do professor"
                        aria-label="Editar perfil do professor"
                        disabled={busy}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir professor"
                        aria-label="Excluir professor"
                        disabled={busy}
                        onClick={() => setModal("delete")}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={modal === "delete"}
                title="Excluir Professor"
                message={
                    <>
                        Excluir permanentemente o professor <strong>{person.name}</strong> e seu acesso ao sistema?
                    </>
                }
                processing={processing === "delete"}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo professor..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!busy) setModal(null);
                }}
                onConfirm={remove}
            />
        </TeacherPage>
    );
}
