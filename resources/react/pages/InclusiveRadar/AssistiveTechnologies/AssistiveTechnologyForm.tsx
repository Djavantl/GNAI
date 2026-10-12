import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import EvidenceUpload from "@/components/Forms/EvidenceUpload/EvidenceUpload";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, {
    FormGridWide,
    FormStack,
} from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import type {
    TechnologyFormOptions,
    TechnologyRecord,
} from "./AssistiveTechnologyForm.types";
import "./AssistiveTechnologyForm.css";

const base = "/inclusive-radar/assistive-technologies";
const today = (): string => {
    const date = new Date();
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
};
type FormData = {
    name: string;
    is_digital: boolean;
    asset_code: string;
    notes: string;
    conservation_state: string;
    quantity: string;
    is_loanable: boolean;
    status: string;
    is_active: boolean;
    deficiencies: number[];
    inspection: {
        type: string;
        date: string;
        description: string;
        evidences: File[];
    };
};

export default function AssistiveTechnologyForm({
    mode,
    options,
    technology,
    cloneSource,
    activeLoans = 0,
    onProcessingChange,
}: {
    mode: "create" | "edit";
    options: TechnologyFormOptions;
    technology?: TechnologyRecord;
    cloneSource?: TechnologyRecord | null;
    activeLoans?: number;
    onProcessingChange?: (value: boolean) => void;
}): React.JSX.Element {
    const source = technology ?? cloneSource;
    const isClone = mode === "create" && !!cloneSource;
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [requestError, setRequestError] = useState("");
    const [editorFailed, setEditorFailed] = useState(false);
    const form = useForm<FormData>({
        name: source?.name ?? "",
        is_digital: source?.is_digital ?? false,
        asset_code: technology?.asset_code ?? "",
        notes: source?.notes ?? "",
        conservation_state: technology?.conservation_state ?? "",
        quantity: String(source?.quantity ?? 1),
        is_loanable: source?.is_loanable ?? true,
        status: source?.status ?? options.defaultStatus ?? "",
        is_active: source?.is_active ?? true,
        deficiencies: source?.deficiencies.map((item) => item.id) ?? [],
        inspection: {
            type: isClone ? "" : options.defaultInspection,
            date: isClone ? "" : today(),
            description: "",
            evidences: [],
        },
    });
    const errors = form.errors as Record<string, string | undefined>;
    const cancelUrl = technology ? `${base}/${technology.id}` : base;
    const optionList = (items: Record<string, string>) =>
        Object.entries(items).map(([value, label]) => ({ value, label }));
    const describedBy = (name: string, id: string) =>
        errors[name] ? `${id}-error` : undefined;
    const setProcessing = (value: boolean): void => onProcessingChange?.(value);

    return (
        <FormLayout
            ref={formElement}
            className="technologyForm"
            aria-label={
                mode === "create"
                    ? "Cadastrar tecnologia assistiva"
                    : "Editar tecnologia assistiva"
            }
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={(event) => {
                event.preventDefault();
                if (
                    !options.can.submit ||
                    busy.current ||
                    form.processing ||
                    editorFailed
                )
                    return;
                busy.current = true;
                setRequestError("");
                setProcessing(true);
                form.transform((data) =>
                    mode === "edit" ? { ...data, _method: "put" } : data,
                );
                form.post(
                    mode === "edit" && technology
                        ? `${base}/${technology.id}`
                        : `${base}/store`,
                    {
                        forceFormData: true,
                        preserveScroll: true,
                        onError: () =>
                            requestAnimationFrame(() =>
                                formElement.current
                                    ?.querySelector<HTMLElement>(
                                        '[aria-invalid="true"]',
                                    )
                                    ?.focus(),
                            ),
                        onHttpException: () => {
                            setRequestError(
                                "Não foi possível concluir a operação. Tente novamente.",
                            );
                            return false;
                        },
                        onNetworkError: () => {
                            setRequestError(
                                "Falha de conexão. Confira sua conexão e tente novamente.",
                            );
                            return false;
                        },
                        onFinish: () => {
                            busy.current = false;
                            setProcessing(false);
                        },
                    },
                );
            }}
        >
            <CardBox>
                {requestError ? (
                    <div role="alert">
                        <InlineAlert variant="danger">
                            {requestError}
                        </InlineAlert>
                    </div>
                ) : null}
                {Object.keys(form.errors).length ? (
                    <div role="alert">
                        <InlineAlert variant="danger">
                            Confira os campos indicados antes de salvar.
                        </InlineAlert>
                    </div>
                ) : null}
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="technology-identification-form"
                        title="Identificação do Recurso"
                        variant="inline"
                        iconClass="fa fa-wheelchair"
                    >
                        <FormGrid>
                            <FormGridWide>
                                <FormField
                                    id="technology-name"
                                    label="Tipo da Tecnologia"
                                    error={errors.name}
                                    requiredIndicator
                                >
                                    <TextInput
                                        id="technology-name"
                                        name="name"
                                        required
                                        maxLength={255}
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        placeholder="Ex: Cadeira de Rodas Motorizada"
                                        aria-describedby={describedBy(
                                            "name",
                                            "technology-name",
                                        )}
                                        onChange={(event) =>
                                            form.setData(
                                                "name",
                                                event.target.value,
                                            )
                                        }
                                    />
                                </FormField>
                            </FormGridWide>
                            <FormField
                                id="technology-digital"
                                label="Natureza do Recurso"
                                error={errors.is_digital}
                                requiredIndicator
                            >
                                <SelectField
                                    id="technology-digital"
                                    name="is_digital"
                                    label="Natureza do Recurso"
                                    required
                                    value={form.data.is_digital ? "1" : "0"}
                                    aria-invalid={!!errors.is_digital}
                                    aria-describedby={describedBy(
                                        "is_digital",
                                        "technology-digital",
                                    )}
                                    options={[
                                        { value: "0", label: "Recurso Físico" },
                                        {
                                            value: "1",
                                            label: "Recurso Digital",
                                        },
                                    ]}
                                    onChange={(event) =>
                                        form.setData((data) => ({
                                            ...data,
                                            is_digital:
                                                event.target.value === "1",
                                            asset_code:
                                                event.target.value === "1"
                                                    ? ""
                                                    : data.asset_code,
                                        }))
                                    }
                                />
                            </FormField>
                            {!form.data.is_digital ? (
                                <FormField
                                    id="technology-asset-code"
                                    label="Patrimônio / Tombamento"
                                    error={errors.asset_code}
                                >
                                    <TextInput
                                        id="technology-asset-code"
                                        name="asset_code"
                                        maxLength={50}
                                        value={form.data.asset_code}
                                        invalid={!!errors.asset_code}
                                        aria-describedby={describedBy(
                                            "asset_code",
                                            "technology-asset-code",
                                        )}
                                        onChange={(event) =>
                                            form.setData(
                                                "asset_code",
                                                event.target.value,
                                            )
                                        }
                                    />
                                </FormField>
                            ) : null}
                            <FormGridWide>
                                <FormField
                                    id="technology-notes-input"
                                    label="Descrição"
                                    error={errors.notes}
                                >
                                    <SummernoteEditor
                                        id="technology-notes"
                                        label="Descrição"
                                        defaultValue={form.data.notes}
                                        height={120}
                                        disabled={form.processing}
                                        invalid={!!errors.notes}
                                        describedBy={describedBy(
                                            "notes",
                                            "technology-notes-input",
                                        )}
                                        onLoadError={() => {
                                            setEditorFailed(true);
                                            form.setError(
                                                "notes",
                                                "Não foi possível carregar o editor de descrição. Atualize a página e tente novamente.",
                                            );
                                        }}
                                        onChange={(html) =>
                                            form.setData("notes", html)
                                        }
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="technology-inspection-form"
                        title={
                            mode === "create"
                                ? "Vistoria Inicial"
                                : "Nova Vistoria"
                        }
                        variant="inline"
                        iconClass="fa fa-search"
                    >
                        <FormGrid>
                            <FormField
                                id="technology-inspection-type"
                                label="Tipo de Inspeção"
                                error={errors["inspection.type"]}
                                requiredIndicator
                            >
                                <SelectField
                                    id="technology-inspection-type"
                                    name="inspection[type]"
                                    label="Tipo de Inspeção"
                                    required
                                    value={form.data.inspection.type}
                                    aria-invalid={!!errors["inspection.type"]}
                                    aria-describedby={describedBy(
                                        "inspection.type",
                                        "technology-inspection-type",
                                    )}
                                    options={optionList(
                                        options.inspectionTypes,
                                    )}
                                    onChange={(event) =>
                                        form.setData("inspection", {
                                            ...form.data.inspection,
                                            type: event.target.value,
                                        })
                                    }
                                />
                            </FormField>
                            <FormField
                                id="technology-inspection-date"
                                label="Data da Inspeção"
                                error={errors["inspection.date"]}
                                requiredIndicator
                            >
                                <DatePicker
                                    id="technology-inspection-date"
                                    name="inspection[date]"
                                    label="Data da Inspeção"
                                    required
                                    disabled={form.processing}
                                    value={form.data.inspection.date}
                                    invalid={!!errors["inspection.date"]}
                                    describedBy={describedBy(
                                        "inspection.date",
                                        "technology-inspection-date",
                                    )}
                                    onChange={(value) =>
                                        form.setData("inspection", {
                                            ...form.data.inspection,
                                            date: value,
                                        })
                                    }
                                />
                            </FormField>
                            <FormField
                                id="technology-conservation"
                                label="Estado de Conservação"
                                error={errors.conservation_state}
                                requiredIndicator
                            >
                                <SelectField
                                    id="technology-conservation"
                                    name="conservation_state"
                                    label="Estado de Conservação"
                                    required
                                    value={form.data.conservation_state}
                                    aria-invalid={!!errors.conservation_state}
                                    aria-describedby={describedBy(
                                        "conservation_state",
                                        "technology-conservation",
                                    )}
                                    options={optionList(
                                        options.conservationStates,
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "conservation_state",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <EvidenceUpload
                                id="technology-evidences"
                                name="inspection[evidences][]"
                                disabled={form.processing}
                                files={form.data.inspection.evidences}
                                error={
                                    errors["inspection.evidences"] ??
                                    errors["inspection.evidences.0"]
                                }
                                onChange={(evidences) => {
                                    form.clearErrors("inspection.evidences");
                                    form.setData("inspection", {
                                        ...form.data.inspection,
                                        evidences,
                                    });
                                }}
                            />
                            <FormGridWide>
                                <FormField
                                    id="technology-inspection-description-input"
                                    label="Parecer Técnico"
                                    error={errors["inspection.description"]}
                                    hint={
                                        mode === "create"
                                            ? "Descreva o estado do item ou detalhes da vistoria inicial."
                                            : "Descreva o motivo da mudança de estado ou detalhes da nova vistoria."
                                    }
                                >
                                    <SummernoteEditor
                                        id="technology-inspection-description"
                                        label="Parecer Técnico"
                                        defaultValue={
                                            form.data.inspection.description
                                        }
                                        height={120}
                                        disabled={form.processing}
                                        invalid={
                                            !!errors["inspection.description"]
                                        }
                                        describedBy={describedBy(
                                            "inspection.description",
                                            "technology-inspection-description-input",
                                        )}
                                        onLoadError={() => {
                                            setEditorFailed(true);
                                            form.setError(
                                                "inspection.description",
                                                "Não foi possível carregar o editor de parecer técnico. Atualize a página e tente novamente.",
                                            );
                                        }}
                                        onChange={(html) =>
                                            form.setData("inspection", {
                                                ...form.data.inspection,
                                                description: html,
                                            })
                                        }
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="technology-management-form"
                        title="Gestão e Público"
                        variant="inline"
                        iconClass="fa fa-cogs"
                    >
                        <FormGrid>
                            <FormStack>
                                <FormField
                                    id="technology-quantity"
                                    label="Quantidade Total"
                                    error={errors.quantity}
                                    requiredIndicator={!form.data.is_digital}
                                >
                                    <TextInput
                                        id="technology-quantity"
                                        name="quantity"
                                        type="number"
                                        min={
                                            form.data.is_digital
                                                ? 0
                                                : Math.max(1, activeLoans)
                                        }
                                        required={!form.data.is_digital}
                                        value={form.data.quantity}
                                        invalid={!!errors.quantity}
                                        aria-describedby={describedBy(
                                            "quantity",
                                            "technology-quantity",
                                        )}
                                        onChange={(event) =>
                                            form.setData(
                                                "quantity",
                                                event.target.value,
                                            )
                                        }
                                    />
                                </FormField>
                                {mode === "edit" && activeLoans > 0 ? (
                                    <InlineAlert variant="warning">
                                        <i
                                            className="fa fa-lock"
                                            aria-hidden="true"
                                        />{" "}
                                        {activeLoans} unidades em uso.
                                    </InlineAlert>
                                ) : null}
                                <ToggleControl
                                    id="technology-loanable"
                                    name="is_loanable"
                                    size="small"
                                    checked={form.data.is_loanable}
                                    disabled={form.processing}
                                    label="Permitir Empréstimos"
                                    description="Marque se este recurso pode ser emprestado"
                                    onChange={(checked) =>
                                        form.setData("is_loanable", checked)
                                    }
                                />
                            </FormStack>
                            <FormStack>
                                <FormField
                                    id="technology-status"
                                    label="Status do Recurso"
                                    error={errors.status}
                                    requiredIndicator
                                >
                                    <SelectField
                                        id="technology-status"
                                        name="status"
                                        label="Status do Recurso"
                                        required
                                        value={form.data.status}
                                        aria-invalid={!!errors.status}
                                        aria-describedby={describedBy(
                                            "status",
                                            "technology-status",
                                        )}
                                        options={optionList(
                                            options.resourceStatuses,
                                        )}
                                        onChange={(event) =>
                                            form.setData(
                                                "status",
                                                event.target.value,
                                            )
                                        }
                                    />
                                </FormField>
                                <ToggleControl
                                    id="technology-active"
                                    name="is_active"
                                    size="small"
                                    checked={form.data.is_active}
                                    disabled={form.processing}
                                    label="Ativar no Sistema"
                                    description="Disponível para visualização e empréstimos"
                                    onChange={(checked) =>
                                        form.setData("is_active", checked)
                                    }
                                />
                            </FormStack>
                            <FormGridWide>
                                <FormField
                                    id="technology-deficiencies"
                                    label="Público Alvo"
                                    error={errors.deficiencies}
                                    requiredIndicator
                                >
                                    <ChoiceGroup
                                        id="technology-deficiencies"
                                        label="Público Alvo"
                                        invalid={!!errors.deficiencies}
                                        describedBy={describedBy(
                                            "deficiencies",
                                            "technology-deficiencies",
                                        )}
                                    >
                                        {options.deficiencies.map(
                                            (deficiency) => (
                                                <ChoiceControl
                                                    key={deficiency.id}
                                                    type="checkbox"
                                                    tone="purple"
                                                    id={`technology-deficiency-${deficiency.id}`}
                                                    name="deficiencies[]"
                                                    label={deficiency.name}
                                                    checked={form.data.deficiencies.includes(
                                                        deficiency.id,
                                                    )}
                                                    onChange={(checked) =>
                                                        form.setData(
                                                            "deficiencies",
                                                            checked
                                                                ? [
                                                                      ...form
                                                                          .data
                                                                          .deficiencies,
                                                                      deficiency.id,
                                                                  ]
                                                                : form.data.deficiencies.filter(
                                                                      (id) =>
                                                                          id !==
                                                                          deficiency.id,
                                                                  ),
                                                        )
                                                    }
                                                />
                                            ),
                                        )}
                                    </ChoiceGroup>
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    form.processing
                        ? form.progress
                            ? `Enviando arquivos: ${form.progress.percentage}%`
                            : "Salvando tecnologia assistiva..."
                        : ""
                }
            >
                <Button
                    href={cancelUrl}
                    tone="cancel"
                    disabled={form.processing}
                >
                    Cancelar
                </Button>
                {options.can.submit ? (
                    <Button
                        type="submit"
                        tone="save"
                        disabled={form.processing || editorFailed}
                    >
                        {form.processing
                            ? "Salvando..."
                            : mode === "create"
                              ? "Cadastrar"
                              : "Salvar"}
                    </Button>
                ) : null}
            </FormActions>
        </FormLayout>
    );
}
