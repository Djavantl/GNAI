import { usePage } from "@inertiajs/react";
import { useMemo } from "react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import SelectField from "@/components/Forms/SelectField/SelectField";
import StudentRecordReturnButton from "@/components/Students/StudentRecordReturnButton";
import { studentsBase } from "@/components/Students/Students.utils";
import TableActions from "@/components/Tables/TableActions/TableActions";
import {
    formatTableListText,
    TableListCard,
    TableListClearButton,
    TableListCreateButton,
    TableListEmpty,
    TableListFilterActions,
    TableListFooter,
    TableListScroll,
    TableListTable,
    useTableListAction,
    useTableListVisit,
    type TableListColumn,
} from "@/components/Tables/TableList/TableList";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import useAutoListFilters from "@/hooks/useAutoListFilters";
import StudentDeficiencyPage from "./StudentDeficiencyPage";
import type {
    StudentDeficiencyFilters,
    StudentDeficiencyListRecord,
    StudentDeficiencyPaginator,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";
import "./StudentDeficiencies.css";

type Props = {
    student: StudentDeficiencyStudent;
    deficiencies: StudentDeficiencyPaginator;
    filterDeficiencies: { value: string; label: string }[];
    filters: Partial<StudentDeficiencyFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
};

const emptyFilters: StudentDeficiencyFilters = {
    deficiency_id: "",
    severity: "",
};
const severityOptions = [
    { value: "mild", label: "Leve" },
    { value: "moderate", label: "Moderada" },
    { value: "severe", label: "Severa" },
];
const columns: TableListColumn[] = [
    { id: "name", label: "Nome", width: "46%", minWidth: 140, fixed: "start" },
    { id: "severity", label: "Severidade", width: "26%", minWidth: 90 },
    { id: "notes", label: "Observações", width: "16%", defaultVisible: false },
    { id: "actions", label: "Ações", width: "28%", minWidth: 104, fixed: "end" },
];

export default function InclusionStudentDeficienciesIndexPage({
    student,
    deficiencies,
    filterDeficiencies,
    filters: initialFilters,
    can,
}: Props): React.JSX.Element {
    const pageErrors = usePage().props.errors as Record<string, string | undefined>;
    const listUrl = `${studentsBase}/${student.id}/deficiencies`;
    const list = useTableListVisit({
        url: listUrl,
        failureText: "Não foi possível atualizar os perfis de atendimento. Tente novamente.",
    });
    const removal = useTableListAction<StudentDeficiencyListRecord>({
        failureText: "Não foi possível excluir o perfil de atendimento. Tente novamente.",
    });
    const initialValues = useMemo(
        () => ({ ...emptyFilters, ...initialFilters }),
        [initialFilters],
    );
    const { values, update, clear, submit, cancelScheduled } = useAutoListFilters({
        initialValues,
        emptyValues: emptyFilters,
        onSearch: (filters) => list.visit(filters),
    });

    return (
        <StudentDeficiencyPage
            title="Perfis de Atendimento do Aluno"
            subtitle={`Aluno: ${student.person.name}`}
            student={student}
            actions={(
                <>
                    <StudentRecordReturnButton
                        studentId={student.id}
                        tab="support-profiles"
                    />
                    {can.create ? (
                        <TableListCreateButton
                            href={`${listUrl}/create`}
                            ariaLabel="Cadastrar perfil de atendimento do aluno"
                        />
                    ) : null}
                </>
            )}
        >
            <TableListCard
                aria-label="Perfis de atendimento do aluno"
                aria-busy={list.loading}
                loading={list.loading}
                loadingLabel="Atualizando perfis de atendimento"
            >
                <FormLayout
                    onSubmit={(event) => {
                        event.preventDefault();
                        submit();
                    }}
                >
                    <div className="studentDeficiencyIndex__filters">
                        <FormField
                            id="student-deficiency-filter-profile"
                            label="Perfil"
                            error={pageErrors.deficiency_id}
                        >
                            <SelectField
                                id="student-deficiency-filter-profile"
                                name="deficiency_id"
                                label="Perfil"
                                placeholder="Todos"
                                value={values.deficiency_id}
                                options={filterDeficiencies}
                                aria-invalid={!!pageErrors.deficiency_id}
                                aria-describedby={pageErrors.deficiency_id ? "student-deficiency-filter-profile-error" : undefined}
                                onChange={(event) => update("deficiency_id", event.target.value)}
                            />
                        </FormField>
                        <FormField
                            id="student-deficiency-filter-severity"
                            label="Severidade"
                            error={pageErrors.severity}
                        >
                            <SelectField
                                id="student-deficiency-filter-severity"
                                name="severity"
                                label="Severidade"
                                placeholder="Todas"
                                value={values.severity}
                                options={severityOptions}
                                aria-invalid={!!pageErrors.severity}
                                aria-describedby={pageErrors.severity ? "student-deficiency-filter-severity-error" : undefined}
                                onChange={(event) => update("severity", event.target.value)}
                            />
                        </FormField>
                        <TableListFilterActions>
                            <TableListClearButton onClick={clear} />
                        </TableListFilterActions>
                    </div>
                </FormLayout>
                <TableListScroll aria-label="Tabela de perfis de atendimento do aluno">
                    <TableListTable
                        columns={columns}
                        page={deficiencies}
                    >
                        {deficiencies.data.length ? deficiencies.data.map((profile) => {
                            const name = profile.deficiency?.name ?? "Perfil não identificado";

                            return (
                                <tr key={profile.id}>
                                    <td>
                                        <strong>{name}</strong>
                                    </td>
                                    <td>
                                        {profile.severity_label ? (
                                            <TableLabel tone={profile.severity_color ?? "inverse"}>
                                                {profile.severity_label}
                                            </TableLabel>
                                        ) : "Não informada"}
                                    </td>
                                    <td>{formatTableListText(profile.notes)}</td>
                                    <td>
                                        {can.view || can.delete ? (
                                            <TableActions>
                                                {can.view ? (
                                                    <Button
                                                        href={`${listUrl}/${profile.id}`}
                                                        size="sm"
                                                        tone="dark-blue"
                                                        iconOnly
                                                        leftIconClass="fa fa-eye"
                                                        aria-label={`Visualizar perfil ${name}`}
                                                    />
                                                ) : null}
                                                {can.delete ? (
                                                    <Button
                                                        tone="dark-red"
                                                        size="sm"
                                                        iconOnly
                                                        leftIconClass="fa fa-trash-o"
                                                        aria-label={`Excluir perfil ${name}`}
                                                        disabled={removal.processing}
                                                        onClick={() => {
                                                            cancelScheduled();
                                                            removal.open(profile);
                                                        }}
                                                    />
                                                ) : null}
                                            </TableActions>
                                        ) : "Nenhuma ação"}
                                    </td>
                                </tr>
                            );
                        }) : (
                            <tr>
                                <TableListEmpty colSpan={columns.length}>
                                    Nenhum perfil de atendimento do aluno encontrado.
                                </TableListEmpty>
                            </tr>
                        )}
                    </TableListTable>
                </TableListScroll>
                <TableListFooter
                    page={deficiencies}
                    disabled={list.loading}
                    paginationLabel="Paginação de perfis de atendimento"
                    onSelect={(page) => list.visit(values, page)}
                />
            </TableListCard>
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Perfil de Atendimento"
                message={(
                    <>
                        Deseja remover o perfil{" "}
                        <strong>{removal.target?.deficiency?.name ?? "selecionado"}</strong>{" "}
                        do registro do aluno?
                    </>
                )}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo perfil..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    cancelScheduled();
                    list.cancel();
                    removal.submit("delete", `${listUrl}/${removal.target.id}`);
                }}
            />
        </StudentDeficiencyPage>
    );
}
