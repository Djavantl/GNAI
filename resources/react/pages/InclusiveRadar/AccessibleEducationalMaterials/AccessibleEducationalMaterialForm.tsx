import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
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
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    MaterialFormOptions,
    MaterialRecord,
    NamedOption,
} from "./AccessibleEducationalMaterialForm.types";

const base = "/inclusive-radar/accessible-educational-materials";

type FormData = {
    name: string;
    is_digital: boolean;
    asset_code: string;
    notes: string;
    accessibility_features: number[];
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

function today(): string {
    const date = new Date();

    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
}

function CheckboxOptions({
    id,
    name,
    label,
    options,
    selected,
    error,
    required = false,
    onChange,
}: {
    id: string;
    name: "accessibility_features" | "deficiencies";
    label: string;
    options: NamedOption[];
    selected: number[];
    error?: string;
    required?: boolean;
    onChange: (values: number[]) => void;
}): React.JSX.Element {
    return (
        <FormField
            id={id}
            label={label}
            error={error}
            requiredIndicator={required}
        >
            <ChoiceGroup
                id={id}
                label={label}
                invalid={!!error}
                describedBy={error ? `${id}-error` : undefined}
            >
                {options.map((option) => (
                    <ChoiceControl
                        key={option.id}
                        type="checkbox"
                        tone="purple"
                        id={`${id}-${option.id}`}
                        name={`${name}[]`}
                        label={option.name}
                        checked={selected.includes(option.id)}
                        onChange={(checked) =>
                            onChange(
                                checked
                                    ? [...selected, option.id]
                                    : selected.filter(
                                          (value) => value !== option.id,
                                      ),
                            )
                        }
                    />
                ))}
            </ChoiceGroup>
        </FormField>
    );
}

export default function AccessibleEducationalMaterialForm({
    mode,
    options,
    material,
    cloneSource,
    activeLoans = 0,
}: {
    mode: "create" | "edit";
    options: MaterialFormOptions;
    material?: MaterialRecord;
    cloneSource?: MaterialRecord | null;
    activeLoans?: number;
}): React.JSX.Element {
    const source = material ?? cloneSource;
    const cloning = mode === "create" && !!cloneSource;
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const form = useForm<FormData>({
        name: source?.name ?? "",
        is_digital: source?.is_digital ?? false,
        asset_code: material?.asset_code ?? "",
        notes: source?.notes ?? "",
        accessibility_features:
            source?.accessibility_features.map((feature) => feature.id) ?? [],
        conservation_state: material?.conservation_state ?? "",
        quantity: String(source?.quantity ?? 1),
        is_loanable: source?.is_loanable ?? true,
        status: source?.status ?? options.defaultStatus ?? "",
        is_active: source?.is_active ?? true,
        deficiencies: source?.deficiencies.map((item) => item.id) ?? [],
        inspection: {
            type: cloning ? "" : options.defaultInspection,
            date: cloning ? "" : today(),
            description: "",
            evidences: [],
        },
    });
    const errors = form.errors as Record<string, string | undefined>;
    const cancelUrl = material ? `${base}/${material.id}` : base;
    const selectOptions = (items: Record<string, string>) =>
        Object.entries(items).map(([value, label]) => ({ value, label }));
    const describedBy = (name: string, id: string) =>
        errors[name] ? `${id}-error` : undefined;

    return (
        <FormLayout
            ref={formElement}
            aria-label={
                mode === "create"
                    ? "Cadastrar material pedagógico acessível"
                    : "Editar material pedagógico acessível"
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
                ) {
                    return;
                }

                busy.current = true;
                form.transform((data) =>
                    mode === "edit" ? { ...data, _method: "put" } : data,
                );
                form.post(
                    mode === "edit" && material
                        ? `${base}/${material.id}`
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
                            notify({
                                tone: "error",
                                title: "Não foi possível concluir",
                                text: "Não foi possível concluir a operação. Tente novamente.",
                            });
                            return false;
                        },
                        onNetworkError: () => {
                            notify({
                                tone: "error",
                                title: "Falha de conexão",
                                text: "Confira sua conexão e tente novamente.",
                            });
                            return false;
                        },
                        onFinish: () => {
                            busy.current = false;
                        },
                    },
                );
            }}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="material-identification-form"
                        title="Identificação do Recurso"
                        variant="inline"
                        iconClass="fa fa-book"
                    >
                        <FormGrid>
                            <FormGridWide>
                                <FormField
                                    id="material-name"
                                    label="Título do Material"
                                    error={errors.name}
                                    requiredIndicator
                                >
                                    <TextInput
                                        id="material-name"
                                        name="name"
                                        required
                                        maxLength={255}
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        placeholder="Ex: Livro em Braille, Maquete Tátil..."
                                        aria-describedby={describedBy(
                                            "name",
                                            "material-name",
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
                                id="material-digital"
                                label="Natureza do Recurso"
                                error={errors.is_digital}
                                requiredIndicator
                            >
                                <SelectField
                                    id="material-digital"
                                    name="is_digital"
                                    label="Natureza do Recurso"
                                    required
                                    value={form.data.is_digital ? "1" : "0"}
                                    aria-invalid={!!errors.is_digital}
                                    aria-describedby={describedBy(
                                        "is_digital",
                                        "material-digital",
                                    )}
                                    options={[
                                        {
                                            value: "0",
                                            label: "Recurso Físico",
                                        },
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
                                    id="material-asset-code"
                                    label="Patrimônio / Tombamento"
                                    error={errors.asset_code}
                                >
                                    <TextInput
                                        id="material-asset-code"
                                        name="asset_code"
                                        maxLength={50}
                                        value={form.data.asset_code}
                                        invalid={!!errors.asset_code}
                                        aria-describedby={describedBy(
                                            "asset_code",
                                            "material-asset-code",
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
                                    id="material-notes-input"
                                    label="Descrição"
                                    error={errors.notes}
                                >
                                    <SummernoteEditor
                                        id="material-notes"
                                        label="Descrição"
                                        defaultValue={form.data.notes}
                                        height={120}
                                        disabled={form.processing}
                                        invalid={!!errors.notes}
                                        describedBy={describedBy(
                                            "notes",
                                            "material-notes-input",
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
                        id="material-accessibility-form"
                        title="Recursos de Acessibilidade"
                        variant="inline"
                        iconClass="fa fa-universal-access"
                    >
                        <CheckboxOptions
                            id="material-accessibility-features"
                            name="accessibility_features"
                            label="Recursos presentes no material"
                            options={options.accessibilityFeatures}
                            selected={form.data.accessibility_features}
                            error={errors.accessibility_features}
                            onChange={(values) =>
                                form.setData("accessibility_features", values)
                            }
                        />
                    </FormSection>
                    <FormSection
                        id="material-inspection-form"
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
                                id="material-inspection-type"
                                label="Tipo de Inspeção"
                                error={errors["inspection.type"]}
                                requiredIndicator
                            >
                                <SelectField
                                    id="material-inspection-type"
                                    name="inspection[type]"
                                    label="Tipo de Inspeção"
                                    required
                                    value={form.data.inspection.type}
                                    aria-invalid={!!errors["inspection.type"]}
                                    aria-describedby={describedBy(
                                        "inspection.type",
                                        "material-inspection-type",
                                    )}
                                    options={selectOptions(
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
                                id="material-inspection-date"
                                label="Data da Inspeção"
                                error={errors["inspection.date"]}
                                requiredIndicator
                            >
                                <DatePicker
                                    id="material-inspection-date"
                                    name="inspection[date]"
                                    label="Data da Inspeção"
                                    required
                                    disabled={form.processing}
                                    value={form.data.inspection.date}
                                    invalid={!!errors["inspection.date"]}
                                    describedBy={describedBy(
                                        "inspection.date",
                                        "material-inspection-date",
                                    )}
                                    onChange={(date) =>
                                        form.setData("inspection", {
                                            ...form.data.inspection,
                                            date,
                                        })
                                    }
                                />
                            </FormField>
                            <FormField
                                id="material-conservation"
                                label="Estado de Conservação"
                                error={errors.conservation_state}
                                requiredIndicator
                            >
                                <SelectField
                                    id="material-conservation"
                                    name="conservation_state"
                                    label="Estado de Conservação"
                                    required
                                    value={form.data.conservation_state}
                                    aria-invalid={!!errors.conservation_state}
                                    aria-describedby={describedBy(
                                        "conservation_state",
                                        "material-conservation",
                                    )}
                                    options={selectOptions(
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
                                id="material-evidences"
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
                                    id="material-inspection-description-input"
                                    label="Parecer Técnico"
                                    error={errors["inspection.description"]}
                                >
                                    <SummernoteEditor
                                        id="material-inspection-description"
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
                                            "material-inspection-description-input",
                                        )}
                                        onLoadError={() => {
                                            setEditorFailed(true);
                                            form.setError(
                                                "inspection.description",
                                                "Não foi possível carregar o editor de parecer técnico. Atualize a página e tente novamente.",
                                            );
                                        }}
                                        onChange={(description) =>
                                            form.setData("inspection", {
                                                ...form.data.inspection,
                                                description,
                                            })
                                        }
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="material-management-form"
                        title="Gestão e Público"
                        variant="inline"
                        iconClass="fa fa-cogs"
                    >
                        <FormGrid>
                            <FormStack>
                                <FormField
                                    id="material-quantity"
                                    label="Quantidade Total"
                                    error={errors.quantity}
                                    requiredIndicator={!form.data.is_digital}
                                >
                                    <TextInput
                                        id="material-quantity"
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
                                            "material-quantity",
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
                                        <i className="fa fa-lock" aria-hidden="true" />{" "}
                                        {activeLoans} unidades em uso.
                                    </InlineAlert>
                                ) : null}
                                <ToggleControl
                                    id="material-loanable"
                                    name="is_loanable"
                                    size="small"
                                    checked={form.data.is_loanable}
                                    disabled={form.processing}
                                    label="Permitir Empréstimos"
                                    labelAddon={(
                                        <InfoTooltip
                                            text="Marque se este material pode ser emprestado."
                                        />
                                    )}
                                    onChange={(checked) =>
                                        form.setData("is_loanable", checked)
                                    }
                                />
                            </FormStack>
                            <FormStack>
                                <FormField
                                    id="material-status"
                                    label="Status do Recurso"
                                    error={errors.status}
                                    requiredIndicator
                                >
                                    <SelectField
                                        id="material-status"
                                        name="status"
                                        label="Status do Recurso"
                                        required
                                        value={form.data.status}
                                        aria-invalid={!!errors.status}
                                        aria-describedby={describedBy(
                                            "status",
                                            "material-status",
                                        )}
                                        options={selectOptions(
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
                                    id="material-active"
                                    name="is_active"
                                    size="small"
                                    checked={form.data.is_active}
                                    disabled={form.processing}
                                    label="Ativar no Sistema"
                                    labelAddon={(
                                        <InfoTooltip
                                            text="Disponível para visualização e empréstimos."
                                        />
                                    )}
                                    onChange={(checked) =>
                                        form.setData("is_active", checked)
                                    }
                                />
                            </FormStack>
                            <FormGridWide>
                                <CheckboxOptions
                                    id="material-deficiencies"
                                    name="deficiencies"
                                    label="Público Alvo"
                                    options={options.deficiencies}
                                    selected={form.data.deficiencies}
                                    error={errors.deficiencies}
                                    required
                                    onChange={(values) =>
                                        form.setData("deficiencies", values)
                                    }
                                />
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
                            : "Salvando material pedagógico acessível..."
                        : ""
                }
            >
                <Button
                    href={cancelUrl}
                    tone="cancel"
                    label="Cancelar"
                    title="Cancelar e voltar"
                    aria-label="Cancelar e voltar"
                    disabled={form.processing}
                />
                {options.can.submit ? (
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar material pedagógico acessível"
                        aria-label={
                            form.processing
                                ? "Salvando material pedagógico acessível"
                                : mode === "create"
                                  ? "Cadastrar material pedagógico acessível"
                                  : "Salvar material pedagógico acessível"
                        }
                        disabled={form.processing || editorFailed}
                    />
                ) : null}
            </FormActions>
        </FormLayout>
    );
}
