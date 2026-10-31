import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    PendencyFormData,
    PendencyFormOptions,
    PendencyRecord,
} from "./PendencyForm.types";
import "./PendencyForm.css";

const pendenciesBase = "/specialized-educational-support/pendencies";

export default function PendencyForm({
    pendency,
    dueDate = "",
    professionals,
    priorities,
}: PendencyFormOptions & {
    pendency?: PendencyRecord;
    dueDate?: string;
}): React.JSX.Element {
    const editing = Boolean(pendency);
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const form = useForm<PendencyFormData>({
        title: pendency?.title ?? "",
        description: pendency?.description ?? "",
        assigned_to: pendency?.assigned_to ? String(pendency.assigned_to) : "",
        priority: pendency?.priority ?? "",
        due_date: dueDate,
    });
    const errors = form.errors as Record<string, string | undefined>;

    const describedBy = (name: keyof PendencyFormData, id: string): string | undefined =>
        errors[name] ? `${id}-error` : undefined;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;

        busy.current = true;
        const options = {
            preserveScroll: true,
            onError: () =>
                requestAnimationFrame(() =>
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus(),
                ),
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: "Não foi possível salvar a pendência. Atualize a página e tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (pendency) {
            form.put(`${pendenciesBase}/${pendency.id}`, options);
        } else {
            form.post(`${pendenciesBase}/store`, options);
        }
    }

    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Pendências", href: pendenciesBase },
    ];

    if (pendency) {
        breadcrumbItems.push({
            label: "Pendência",
            href: `${pendenciesBase}/${pendency.id}/show`,
        });
    }

    breadcrumbItems.push({ label: editing ? "Editar" : "Cadastrar" });

    return (
        <BusinessPage
            title={editing ? "Editar Pendência" : "Criar Nova Pendência"}
            subtitle={editing
                ? "Atualize os dados da pendência."
                : "Registre uma nova pendência e direcione-a a um profissional responsável."}
            breadcrumbItems={breadcrumbItems}
            className="pendencyFormPage"
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-label={editing ? "Editar pendência" : "Cadastrar pendência"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="pendency-data"
                            title="Dados da Pendência"
                            variant="inline"
                            iconClass="fa fa-tasks"
                        >
                            <FormGrid columns={4}>
                                <FormGridWide>
                                    <FormField
                                        id="pendency-title"
                                        label="Título"
                                        requiredIndicator
                                        error={errors.title}
                                    >
                                        <TextInput
                                            id="pendency-title"
                                            name="title"
                                            required
                                            maxLength={255}
                                            value={form.data.title}
                                            invalid={!!errors.title}
                                            aria-describedby={describedBy("title", "pendency-title")}
                                            onChange={event => form.setData("title", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <FormField
                                        id="pendency-description"
                                        label="Descrição"
                                        error={errors.description}
                                    >
                                        <TextArea
                                            id="pendency-description"
                                            name="description"
                                            rows={4}
                                            value={form.data.description}
                                            aria-invalid={!!errors.description}
                                            aria-describedby={describedBy("description", "pendency-description")}
                                            onChange={event => form.setData("description", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormField
                                    id="pendency-assigned-to"
                                    label="Profissional Responsável"
                                    requiredIndicator
                                    error={errors.assigned_to}
                                    className="pendencyForm__half"
                                >
                                    <SelectField
                                        id="pendency-assigned-to"
                                        name="assigned_to"
                                        label="Profissional Responsável"
                                        required
                                        disabled={form.processing}
                                        value={form.data.assigned_to}
                                        options={professionals.map(professional => ({
                                            value: String(professional.id),
                                            label: professional.person?.name ?? "",
                                        }))}
                                        aria-invalid={!!errors.assigned_to}
                                        aria-describedby={describedBy("assigned_to", "pendency-assigned-to")}
                                        onChange={event => form.setData("assigned_to", event.target.value)}
                                    />
                                </FormField>
                                <FormField
                                    id="pendency-priority"
                                    label="Prioridade"
                                    requiredIndicator
                                    error={errors.priority}
                                >
                                    <SelectField
                                        id="pendency-priority"
                                        name="priority"
                                        label="Prioridade"
                                        required
                                        disabled={form.processing}
                                        value={form.data.priority}
                                        options={Object.entries(priorities).map(([value, label]) => ({
                                            value,
                                            label,
                                        }))}
                                        aria-invalid={!!errors.priority}
                                        aria-describedby={describedBy("priority", "pendency-priority")}
                                        onChange={event => form.setData("priority", event.target.value)}
                                    />
                                </FormField>
                                <FormField
                                    id="pendency-due-date"
                                    label="Data de Vencimento"
                                    error={errors.due_date}
                                >
                                    <DatePicker
                                        id="pendency-due-date"
                                        name="due_date"
                                        label="Data de Vencimento"
                                        disabled={form.processing}
                                        value={form.data.due_date}
                                        invalid={!!errors.due_date}
                                        describedBy={describedBy("due_date", "pendency-due-date")}
                                        onChange={value => form.setData("due_date", value)}
                                    />
                                </FormField>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing ? "Salvando pendência..." : undefined}
                >
                    <Button
                        href={pendenciesBase}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar pendência"
                        aria-label={form.processing ? "Salvando pendência" : "Salvar pendência"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
