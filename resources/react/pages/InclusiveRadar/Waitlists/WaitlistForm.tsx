import { useForm } from "@inertiajs/react";
import CardBox from "@/components/UI/CardBox/CardBox";
import Button from "@/components/UI/Button/Button";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import WaitlistPage from "./WaitlistPage";
import WaitlistResourceDetails from "./WaitlistResourceDetails";
import type {
    WaitlistFormProps,
    WaitlistResource,
} from "./Waitlist.types";
import "./Waitlists.css";

const base = "/inclusive-radar/waitlists";

function optionList(value: Record<string, string> | { id: number; name: string }[]): { value: string; label: string }[] {
    if (Array.isArray(value)) {
        return value.map((option) => ({ value: String(option.id), label: option.name }));
    }

    return Object.entries(value).map(([id, label]) => ({ value: id, label }));
}

function itemOptions(items: WaitlistResource[] | undefined): { value: string; label: string }[] {
    return (items ?? []).map((item) => {
        const label = item.name || item.title || item.description || "Item sem identificação";
        return {
            value: String(item.id),
            label: `${label} (${item.asset_code || "S/N"})`,
        };
    });
}

export default function WaitlistForm({
    students,
    professionals,
    authUser,
    assistive_technologies = [],
    educational_materials = [],
    waitlist,
    statusLabel,
    can,
}: WaitlistFormProps): React.JSX.Element {
    const editing = Boolean(waitlist);
    const form = useForm({
        waitlistable_type: "",
        waitlistable_id: "",
        student_id: "",
        professional_id: "",
        observation: waitlist?.observation ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const studentOptions = optionList(students);
    const professionalOptions = optionList(professionals);
    const resources = form.data.waitlistable_type === "assistive_technology"
        ? itemOptions(assistive_technologies)
        : form.data.waitlistable_type === "accessible_educational_material"
            ? itemOptions(educational_materials)
            : [];

    function updateStudent(value: string): void {
        form.setData((data) => ({
            ...data,
            student_id: value,
            professional_id: value ? "" : data.professional_id,
        }));
    }

    function updateProfessional(value: string): void {
        form.setData((data) => ({
            ...data,
            professional_id: value,
            student_id: value ? "" : data.student_id,
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (!can.submit || form.processing) return;

        if (waitlist) {
            form.transform((data) => ({ observation: data.observation }));
            form.put(`${base}/${waitlist.id}`);
        } else {
            form.post(`${base}/store`);
        }
    }

    return (
        <WaitlistPage
            title={editing ? "Editar Solicitação de Fila" : "Nova Solicitação de Fila"}
            subtitle={
                editing
                    ? "Atualize as informações. Os dados de identificação permanecem bloqueados para preservar o histórico."
                    : "Registre um beneficiário para um recurso atualmente indisponível. Selecione apenas um: aluno ou profissional."
            }
            current={editing ? "Editar" : "Cadastrar"}
            waitlistId={waitlist?.id}
        >
            <FormLayout onSubmit={submit} className="waitlistForm">
                <CardBox className="waitlistForm__card">
                    {waitlist ? (
                        <FormSection
                            id="waitlist-resource"
                            title="Recurso Solicitado"
                            variant="inline"
                            iconClass={waitlist.waitlistable_type === "assistive_technology" ? "fa fa-microchip" : "fa fa-book"}
                        >
                            <WaitlistResourceDetails {...waitlist} />
                        </FormSection>
                    ) : (
                        <FormSection
                            id="waitlist-resource"
                            title="Seleção do Recurso"
                            variant="inline"
                            iconClass="fa fa-cubes"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="waitlist-resource-type"
                                    label="Tipo de Recurso"
                                    requiredIndicator
                                    error={errors.waitlistable_type}
                                >
                                    <SelectField
                                        id="waitlist-resource-type"
                                        name="waitlistable_type"
                                        label="Tipo de Recurso"
                                        required
                                        value={form.data.waitlistable_type}
                                        options={[
                                            { value: "assistive_technology", label: "Tecnologia Assistiva" },
                                            { value: "accessible_educational_material", label: "Material Pedagógico" },
                                        ]}
                                        aria-invalid={!!errors.waitlistable_type}
                                        aria-describedby={errors.waitlistable_type ? "waitlist-resource-type-error" : undefined}
                                        onChange={(event) => form.setData((data) => ({
                                            ...data,
                                            waitlistable_type: event.target.value,
                                            waitlistable_id: "",
                                        }))}
                                    />
                                </FormField>
                                <FormField
                                    id="waitlist-resource-item"
                                    label="Item Específico"
                                    requiredIndicator
                                    error={errors.waitlistable_id}
                                >
                                    <SelectField
                                        id="waitlist-resource-item"
                                        name="waitlistable_id"
                                        label="Item Específico"
                                        required
                                        value={form.data.waitlistable_id}
                                        placeholder={form.data.waitlistable_type ? "Selecione o item" : "Selecione o tipo primeiro"}
                                        options={resources}
                                        disabled={!form.data.waitlistable_type}
                                        aria-invalid={!!errors.waitlistable_id}
                                        aria-describedby={errors.waitlistable_id ? "waitlist-resource-item-error" : undefined}
                                        onChange={(event) => form.setData("waitlistable_id", event.target.value)}
                                    />
                                </FormField>
                            </FormGrid>
                        </FormSection>
                    )}
                    <FormSection
                        id="waitlist-beneficiary"
                        title={waitlist ? "Beneficiário e Responsável" : "Solicitante"}
                        variant="inline"
                        iconClass="fa fa-users"
                    >
                        {waitlist ? (
                            <dl className="waitlistForm__details">
                                <DetailField label="Aluno (Beneficiário)">
                                    {waitlist.student
                                        ? `${waitlist.student.name}${waitlist.student.registration ? ` (Matrícula: ${waitlist.student.registration})` : ""}`
                                        : "Não informado"}
                                </DetailField>
                                <DetailField label="Profissional (Beneficiário)">
                                    {waitlist.professional?.name ?? "Não informado"}
                                </DetailField>
                                <DetailField label="Usuário Responsável">
                                    {authUser.name}
                                </DetailField>
                                <DetailField label="Status Atual">
                                    {statusLabel ?? waitlist.status_label}
                                </DetailField>
                            </dl>
                        ) : (
                            <>
                                <FormGrid columns={2}>
                                    <FormField
                                        id="waitlist-student"
                                        label="Aluno (Beneficiário)"
                                        error={errors.student_id}
                                    >
                                        <SelectField
                                            id="waitlist-student"
                                            name="student_id"
                                            label="Aluno (Beneficiário)"
                                            value={form.data.student_id}
                                            options={studentOptions}
                                            disabled={!!form.data.professional_id}
                                            aria-invalid={!!errors.student_id}
                                            aria-describedby={errors.student_id ? "waitlist-student-error" : undefined}
                                            onChange={(event) => updateStudent(event.target.value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id="waitlist-professional"
                                        label="Profissional (Beneficiário)"
                                        error={errors.professional_id}
                                    >
                                        <SelectField
                                            id="waitlist-professional"
                                            name="professional_id"
                                            label="Profissional (Beneficiário)"
                                            value={form.data.professional_id}
                                            options={professionalOptions}
                                            disabled={!!form.data.student_id}
                                            aria-invalid={!!errors.professional_id}
                                            aria-describedby={errors.professional_id ? "waitlist-professional-error" : undefined}
                                            onChange={(event) => updateProfessional(event.target.value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id="waitlist-user"
                                        label="Usuário Responsável pelo Registro"
                                        className="waitlistForm__wide"
                                    >
                                        <TextInput
                                            id="waitlist-user"
                                            value={authUser.name}
                                            disabled
                                            readOnly
                                        />
                                    </FormField>
                                </FormGrid>
                                <p className="waitlistForm__hint">
                                    Selecione um aluno ou um profissional como beneficiário, nunca ambos.
                                </p>
                            </>
                        )}
                    </FormSection>
                    <FormSection
                        id="waitlist-observation"
                        title="Observações"
                        variant="inline"
                        iconClass="fa fa-comment"
                    >
                        <FormField
                            id="waitlist-observation-field"
                            label={waitlist ? "Notas e Prioridades" : "Observações Adicionais"}
                            error={errors.observation}
                        >
                            <TextArea
                                id="waitlist-observation-field"
                                name="observation"
                                rows={3}
                                value={form.data.observation}
                                placeholder={waitlist
                                    ? "Adicione informações relevantes sobre esta solicitação..."
                                    : "Relate o motivo da urgência ou detalhes da solicitação..."}
                                aria-invalid={!!errors.observation}
                                aria-describedby={errors.observation ? "waitlist-observation-field-error" : undefined}
                                onChange={(event) => form.setData("observation", event.target.value)}
                            />
                        </FormField>
                    </FormSection>
                </CardBox>
                <FormActions>
                    <Button
                        href={waitlist ? `${base}/${waitlist.id}` : base}
                        tone="cancel"
                        label="Cancelar"
                        title={waitlist ? "Cancelar e voltar à solicitação" : "Cancelar e voltar à fila de espera"}
                        aria-label={waitlist ? "Cancelar e voltar à solicitação" : "Cancelar e voltar à fila de espera"}
                        disabled={form.processing}
                    />
                    {can.submit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={waitlist ? "Salvar" : "Cadastrar"}
                            title={waitlist ? "Salvar alterações" : "Cadastrar solicitação"}
                            aria-label={waitlist ? "Salvar alterações" : "Cadastrar solicitação"}
                            disabled={form.processing}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </WaitlistPage>
    );
}
