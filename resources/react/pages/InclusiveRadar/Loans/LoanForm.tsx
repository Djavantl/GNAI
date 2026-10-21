import { useMemo, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, {
    FormGridWide,
} from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import ClockPicker from "@/components/Forms/ClockPicker/ClockPicker";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import type {
    LoanFormProps,
    LoanableItem,
    LoanRecord,
} from "./Loan.types";
import "./Loan.css";

const base = "/inclusive-radar/loans";

type FormData = {
    loanable_type: string;
    loanable_id: string;
    student_id: string;
    professional_id: string;
    loan_date: string;
    due_date: string;
    observation: string;
};

function localDateTime(): string {
    const date = new Date();
    const pad = (value: number) => String(value).padStart(2, "0");

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function optionMap(options: Record<string, string>) {
    return Object.entries(options).map(([value, label]) => ({ value, label }));
}

export default function LoanForm({
    mode,
    students,
    professionals,
    authUser,
    can,
    assistiveTechnologies = [],
    educationalMaterials = [],
    selectedStudentId,
    selectedProfessionalId,
    selectedItemId,
    selectedItemType,
    loan,
}: LoanFormProps & {
    mode: "create" | "edit";
    assistiveTechnologies?: LoanableItem[];
    educationalMaterials?: LoanableItem[];
    selectedStudentId?: number | null;
    selectedProfessionalId?: number | null;
    selectedItemId?: number | null;
    selectedItemType?: string | null;
    loan?: LoanRecord;
}): React.JSX.Element {
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const form = useForm<FormData>({
        loanable_type: selectedItemType ?? "",
        loanable_id: selectedItemId ? String(selectedItemId) : "",
        student_id: selectedStudentId ? String(selectedStudentId) : "",
        professional_id: selectedProfessionalId
            ? String(selectedProfessionalId)
            : "",
        loan_date: localDateTime(),
        due_date: "",
        observation: loan?.observation ?? "",
    });
    const initialLoanTime = useRef(
        form.data.loan_date.split("T")[1] ?? "",
    );
    const [loanTime, setLoanTime] = useState(initialLoanTime.current);
    const errors = form.errors as Record<string, string | undefined>;
    const availableItems = useMemo(() => {
        if (form.data.loanable_type === "assistive_technology") {
            return assistiveTechnologies;
        }
        if (form.data.loanable_type === "accessible_educational_material") {
            return educationalMaterials;
        }
        return [];
    }, [
        assistiveTechnologies,
        educationalMaterials,
        form.data.loanable_type,
    ]);
    const itemOptions = availableItems.map((item) => ({
        value: String(item.id),
        label: item.is_digital
            ? `${item.name} (Digital)`
            : `${item.name} — Patrimônio: ${item.asset_code} — ${item.quantity_available ?? 0} disponível(is)`,
    }));
    const describedBy = (name: string, id: string) =>
        errors[name] ? `${id}-error` : undefined;

    function focusFirstError(): void {
        window.requestAnimationFrame(() => {
            formElement.current
                ?.querySelector<HTMLElement>("[aria-invalid='true']")
                ?.focus();
        });
    }

    return (
        <FormLayout
            ref={formElement}
            className="loanForm"
            aria-label={
                mode === "create"
                    ? "Registrar novo empréstimo"
                    : "Editar registro de empréstimo"
            }
            aria-busy={form.processing}
            onSubmit={(event) => {
                event.preventDefault();
                if (!can.submit || busy.current || form.processing) return;

                busy.current = true;
                const options = {
                    preserveScroll: true,
                    onError: focusFirstError,
                    onFinish: () => {
                        busy.current = false;
                    },
                };

                if (mode === "create") {
                    form.post(`${base}/store`, options);
                } else if (loan) {
                    form.transform((data) => ({
                        observation: data.observation,
                    }));
                    form.put(`${base}/${loan.id}`, options);
                }
            }}
        >
            <CardBox>
                <FormSection
                    id="loan-resource"
                    title="Recurso Emprestado"
                    variant="inline"
                    iconClass="fa fa-exchange"
                >
                    {mode === "create" ? (
                        <FormGrid>
                            <FormField
                                id="loanable-type"
                                label="Tipo de Recurso"
                                error={errors.loanable_type}
                                requiredIndicator
                            >
                                <SelectField
                                    id="loanable-type"
                                    name="loanable_type"
                                    label="Tipo de Recurso"
                                    required
                                    value={form.data.loanable_type}
                                    options={[
                                        {
                                            value: "assistive_technology",
                                            label: "Tecnologia Assistiva",
                                        },
                                        {
                                            value: "accessible_educational_material",
                                            label: "Material Pedagógico Acessível",
                                        },
                                    ]}
                                    aria-invalid={!!errors.loanable_type}
                                    aria-describedby={describedBy(
                                        "loanable_type",
                                        "loanable-type",
                                    )}
                                    onChange={(event) => {
                                        form.setData((data) => ({
                                            ...data,
                                            loanable_type: event.target.value,
                                            loanable_id: "",
                                        }));
                                        form.clearErrors(
                                            "loanable_type",
                                            "loanable_id",
                                        );
                                    }}
                                />
                            </FormField>
                            <FormField
                                id="loanable-item"
                                label="Item Específico"
                                error={errors.loanable_id}
                                requiredIndicator
                            >
                                <SelectField
                                    id="loanable-item"
                                    name="loanable_id"
                                    label="Item Específico"
                                    required
                                    disabled={!form.data.loanable_type}
                                    placeholder={
                                        form.data.loanable_type
                                            ? "Selecione"
                                            : "Selecione o tipo primeiro"
                                    }
                                    value={form.data.loanable_id}
                                    options={itemOptions}
                                    aria-invalid={!!errors.loanable_id}
                                    aria-describedby={describedBy(
                                        "loanable_id",
                                        "loanable-item",
                                    )}
                                    onChange={(event) => {
                                        form.setData(
                                            "loanable_id",
                                            event.target.value,
                                        );
                                        form.clearErrors("loanable_id");
                                    }}
                                />
                            </FormField>
                        </FormGrid>
                    ) : loan ? (
                        <FormGrid>
                            <FormField
                                id="loan-resource-name"
                                label="Recurso Emprestado"
                            >
                                <TextInput
                                    id="loan-resource-name"
                                    value={loan.loanable.name}
                                    disabled
                                />
                            </FormField>
                            <FormField
                                id="loan-resource-type"
                                label="Tipo de Recurso"
                            >
                                <TextInput
                                    id="loan-resource-type"
                                    value={loan.loanable_type_label}
                                    disabled
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="loan-resource-asset-code"
                                    label="Patrimônio / Tombamento"
                                >
                                    <TextInput
                                        id="loan-resource-asset-code"
                                        value={
                                            loan.loanable.asset_code ||
                                            "Não se aplica"
                                        }
                                        disabled
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    ) : null}
                </FormSection>

                <FormSection
                    id="loan-beneficiary"
                    title="Beneficiário e Responsável"
                    variant="inline"
                    iconClass="fa fa-users"
                >
                    {mode === "create" ? (
                        <FormGrid>
                            <FormField
                                id="loan-student"
                                label="Estudante (Beneficiário)"
                                error={errors.student_id}
                            >
                                <SelectField
                                    id="loan-student"
                                    name="student_id"
                                    label="Estudante (Beneficiário)"
                                    value={form.data.student_id}
                                    options={optionMap(students)}
                                    aria-invalid={!!errors.student_id}
                                    aria-describedby={describedBy(
                                        "student_id",
                                        "loan-student",
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "student_id",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <FormField
                                id="loan-professional"
                                label="Profissional (Beneficiário)"
                                error={errors.professional_id}
                            >
                                <SelectField
                                    id="loan-professional"
                                    name="professional_id"
                                    label="Profissional (Beneficiário)"
                                    value={form.data.professional_id}
                                    options={optionMap(professionals)}
                                    aria-invalid={!!errors.professional_id}
                                    aria-describedby={describedBy(
                                        "professional_id",
                                        "loan-professional",
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "professional_id",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="loan-user"
                                    label="Usuário Autenticado (Responsável)"
                                >
                                    <TextInput
                                        id="loan-user"
                                        value={authUser.name}
                                        disabled
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    ) : loan ? (
                        <FormGrid>
                            <FormField
                                id="loan-student"
                                label="Estudante (Beneficiário)"
                            >
                                <TextInput
                                    id="loan-student"
                                    value={
                                        loan.student?.name ?? "Não se aplica"
                                    }
                                    disabled
                                />
                            </FormField>
                            <FormField
                                id="loan-professional"
                                label="Profissional (Beneficiário)"
                            >
                                <TextInput
                                    id="loan-professional"
                                    value={
                                        loan.professional?.name ??
                                        "Não se aplica"
                                    }
                                    disabled
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="loan-user"
                                    label="Usuário Responsável"
                                >
                                    <TextInput
                                        id="loan-user"
                                        value={loan.user.name}
                                        disabled
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    ) : null}
                </FormSection>

                <FormSection
                    id="loan-deadlines"
                    title={
                        mode === "create"
                            ? "Prazos e Observações"
                            : "Prazos e Status"
                    }
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <FormGrid>
                        {mode === "create" ? (
                            <>
                                <div className="loanForm__dateTimeFields">
                                    <FormField
                                        id="loan-date"
                                        label="Data de Saída"
                                        error={errors.loan_date}
                                        requiredIndicator
                                    >
                                        <DatePicker
                                            id="loan-date"
                                            name="loan_date"
                                            label="Data de Saída"
                                            required
                                            value={form.data.loan_date.split("T")[0] ?? ""}
                                            invalid={!!errors.loan_date}
                                            describedBy={describedBy(
                                                "loan_date",
                                                "loan-date",
                                            )}
                                            onChange={(date) =>
                                                form.setData(
                                                    "loan_date",
                                                    date
                                                        ? `${date}T${loanTime}`
                                                        : "",
                                                )
                                            }
                                        />
                                    </FormField>
                                    <FormField
                                        id="loan-time"
                                        label="Horário"
                                        requiredIndicator
                                    >
                                        <ClockPicker
                                            id="loan-time"
                                            label="Horário da Saída"
                                            defaultValue={initialLoanTime.current}
                                            required
                                            onChange={(time) => {
                                                setLoanTime(time);
                                                const date = form.data.loan_date.split("T")[0];
                                                form.setData(
                                                    "loan_date",
                                                    date ? `${date}T${time}` : "",
                                                );
                                            }}
                                        />
                                    </FormField>
                                </div>
                                <FormField
                                    id="loan-due-date"
                                    label="Previsão de Devolução"
                                    hint="Defina o prazo limite para a entrega do item."
                                    error={errors.due_date}
                                    requiredIndicator
                                >
                                    <DatePicker
                                        id="loan-due-date"
                                        name="due_date"
                                        label="Previsão de Devolução"
                                        required
                                        value={form.data.due_date}
                                        invalid={!!errors.due_date}
                                        describedBy={
                                            errors.due_date
                                                ? "loan-due-date-error"
                                                : "loan-due-date-hint"
                                        }
                                        onChange={(date) =>
                                            form.setData("due_date", date)
                                        }
                                    />
                                </FormField>
                            </>
                        ) : loan ? (
                            <>
                                <FormField id="loan-date" label="Data de Saída">
                                    <TextInput
                                        id="loan-date"
                                        value={loan.loan_date_label}
                                        disabled
                                    />
                                </FormField>
                                <FormField
                                    id="loan-due-date"
                                    label="Previsão de Devolução"
                                >
                                    <TextInput
                                        id="loan-due-date"
                                        value={loan.due_date_label}
                                        disabled
                                    />
                                </FormField>
                                <FormField id="loan-status" label="Status Atual">
                                    <TextInput
                                        id="loan-status"
                                        value={loan.status_label}
                                        disabled
                                    />
                                </FormField>
                                <FormField
                                    id="loan-return-date"
                                    label="Data Real da Devolução"
                                >
                                    <TextInput
                                        id="loan-return-date"
                                        value={
                                            loan.return_date_label ??
                                            "Ainda não devolvido"
                                        }
                                        disabled
                                    />
                                </FormField>
                            </>
                        ) : null}
                        <FormGridWide>
                            <FormField
                                id="loan-observation"
                                label="Observações / Estado do Item"
                                error={errors.observation}
                            >
                                <TextArea
                                    id="loan-observation"
                                    name="observation"
                                    rows={4}
                                    value={form.data.observation}
                                    placeholder={
                                        mode === "create"
                                            ? "Anote detalhes sobre o estado de conservação no momento da entrega..."
                                            : "Relate o estado do item..."
                                    }
                                    aria-invalid={
                                        errors.observation ? true : undefined
                                    }
                                    aria-describedby={describedBy(
                                        "observation",
                                        "loan-observation",
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "observation",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                        </FormGridWide>
                    </FormGrid>
                </FormSection>
            </CardBox>
            <FormActions
                status={form.processing ? "Salvando empréstimo..." : undefined}
            >
                <Button
                    href={loan ? `${base}/${loan.id}` : base}
                    tone="cancel"
                    label="Cancelar"
                    disabled={form.processing}
                />
                {can.submit ? (
                    <Button
                        type="submit"
                        tone="save"
                        label={
                            mode === "create"
                                ? "Cadastrar"
                                : "Salvar observações"
                        }
                        disabled={form.processing}
                    />
                ) : null}
            </FormActions>
        </FormLayout>
    );
}
