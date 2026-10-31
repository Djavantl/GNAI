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
    ItemFormOptions,
    ItemRecord,
    NamedOption,
} from "./ItemForm.types";
import { itemsBase as base } from "./Items.types";

type FormData = {
    item_type: string;
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

export default function ItemForm({
    mode,
    options,
    item,
    cloneSource,
    activeLoans = 0,
}: {
    mode: "create" | "edit";
    options: ItemFormOptions;
    item?: ItemRecord;
    cloneSource?: ItemRecord | null;
    activeLoans?: number;
}): React.JSX.Element {
    const source = item ?? cloneSource;
    const cloning = mode === "create" && !!cloneSource;
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const form = useForm<FormData>({
        item_type: source?.item_type ?? "",
        name: source?.name ?? "",
        is_digital: source?.is_digital ?? false,
        asset_code: item?.asset_code ?? "",
        notes: source?.notes ?? "",
        accessibility_features:
            source?.accessibility_features.map((feature) => feature.id) ?? [],
        conservation_state: item?.conservation_state ?? "",
        quantity: String(source?.quantity ?? 1),
        is_loanable: source?.is_loanable ?? true,
        status: source?.status ?? options.defaultStatus ?? "",
        is_active: source?.is_active ?? true,
        deficiencies:
            source?.deficiencies.map((deficiency) => deficiency.id) ?? [],
        inspection: {
            type: cloning ? "" : options.defaultInspection,
            date: cloning ? "" : today(),
            description: "",
            evidences: [],
        },
    });
    const errors = form.errors as Record<string, string | undefined>;
    const cancelUrl = item ? `${base}/${item.id}` : base;
    const selectOptions = (entries: Record<string, string>) =>
        Object.entries(entries).map(([value, label]) => ({ value, label }));
    const describedBy = (name: string, id: string) =>
        errors[name] ? `${id}-error` : undefined;

    return (
        <FormLayout
            ref={formElement}
            aria-label={
                mode === "create"
                    ? "Cadastrar item"
                    : "Editar item"
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
                    mode === "edit" && item
                        ? `${base}/${item.id}`
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
                        id="item-identification-form"
                        title="Identificação"
                        variant="inline"
                        iconClass="fa fa-cube"
                    >
                        <FormGrid>
                            <FormField
                                id="item-type"
                                label="Tipo do Item"
                                error={errors.item_type}
                                requiredIndicator
                            >
                                <SelectField
                                    id="item-type"
                                    name="item_type"
                                    label="Tipo do Item"
                                    placeholder="Selecione"
                                    required
                                    value={form.data.item_type}
                                    aria-invalid={!!errors.item_type}
                                    aria-describedby={describedBy(
                                        "item_type",
                                        "item-type",
                                    )}
                                    options={selectOptions(options.itemTypes)}
                                    onChange={(event) =>
                                        form.setData(
                                            "item_type",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <FormField
                                id="item-name"
                                label="Descrição do Item"
                                error={errors.name}
                                requiredIndicator
                            >
                                <TextInput
                                    id="item-name"
                                    name="name"
                                    required
                                    maxLength={255}
                                    value={form.data.name}
                                    invalid={!!errors.name}
                                    placeholder="Ex: Cadeira de Rodas Motorizada, Livro em Braille..."
                                    aria-describedby={describedBy(
                                        "name",
                                        "item-name",
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "name",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <FormField
                                id="item-digital"
                                label="Natureza do Recurso"
                                error={errors.is_digital}
                                requiredIndicator
                            >
                                <SelectField
                                    id="item-digital"
                                    name="is_digital"
                                    label="Natureza do Recurso"
                                    required
                                    value={form.data.is_digital ? "1" : "0"}
                                    aria-invalid={!!errors.is_digital}
                                    aria-describedby={describedBy(
                                        "is_digital",
                                        "item-digital",
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
                                    id="item-asset-code"
                                    label="Patrimônio / Tombamento"
                                    error={errors.asset_code}
                                >
                                    <TextInput
                                        id="item-asset-code"
                                        name="asset_code"
                                        maxLength={50}
                                        value={form.data.asset_code}
                                        invalid={!!errors.asset_code}
                                        aria-describedby={describedBy(
                                            "asset_code",
                                            "item-asset-code",
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
                                    id="item-notes-input"
                                    label="Descrição Detalhada"
                                    error={errors.notes}
                                >
                                    <SummernoteEditor
                                        id="item-notes"
                                        label="Descrição Detalhada"
                                        defaultValue={form.data.notes}
                                        height={120}
                                        disabled={form.processing}
                                        invalid={!!errors.notes}
                                        describedBy={describedBy(
                                            "notes",
                                            "item-notes-input",
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
                            <FormGridWide>
                                <CheckboxOptions
                                    id="item-accessibility-features"
                                    name="accessibility_features"
                                    label="Recursos presentes no item"
                                    options={options.accessibilityFeatures}
                                    selected={form.data.accessibility_features}
                                    error={errors.accessibility_features}
                                    onChange={(values) =>
                                        form.setData("accessibility_features", values)
                                    }
                                />
                            </FormGridWide>
                            <FormGridWide>
                                <CheckboxOptions
                                    id="item-deficiencies"
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
                    <FormSection
                        id="item-management-form"
                        title="Gestão"
                        variant="inline"
                        iconClass="fa fa-tasks"
                    >
                        <FormGrid>
                            <FormStack>
                                <FormField
                                    id="item-quantity"
                                    label="Quantidade Total"
                                    error={errors.quantity}
                                    requiredIndicator={!form.data.is_digital}
                                >
                                    <TextInput
                                        id="item-quantity"
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
                                            "item-quantity",
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
                            </FormStack>
                            <FormField
                                id="item-status"
                                label="Status do Recurso"
                                error={errors.status}
                                requiredIndicator
                            >
                                <SelectField
                                    id="item-status"
                                    name="status"
                                    label="Status do Recurso"
                                    required
                                    value={form.data.status}
                                    aria-invalid={!!errors.status}
                                    aria-describedby={describedBy(
                                        "status",
                                        "item-status",
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
                                id="item-loanable"
                                name="is_loanable"
                                size="small"
                                checked={form.data.is_loanable}
                                disabled={form.processing}
                                label="Permitir Empréstimos"
                                labelAddon={(
                                    <InfoTooltip
                                        text="Marque se este item pode ser emprestado."
                                    />
                                )}
                                onChange={(checked) =>
                                    form.setData("is_loanable", checked)
                                }
                            />
                            <ToggleControl
                                id="item-active"
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
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="item-inspection-form"
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
                                id="item-inspection-type"
                                label="Tipo de Inspeção"
                                error={errors["inspection.type"]}
                                requiredIndicator
                            >
                                <SelectField
                                    id="item-inspection-type"
                                    name="inspection[type]"
                                    label="Tipo de Inspeção"
                                    required
                                    value={form.data.inspection.type}
                                    aria-invalid={!!errors["inspection.type"]}
                                    aria-describedby={describedBy(
                                        "inspection.type",
                                        "item-inspection-type",
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
                                id="item-inspection-date"
                                label="Data da Inspeção"
                                error={errors["inspection.date"]}
                                requiredIndicator
                            >
                                <DatePicker
                                    id="item-inspection-date"
                                    name="inspection[date]"
                                    label="Data da Inspeção"
                                    required
                                    disabled={form.processing}
                                    value={form.data.inspection.date}
                                    invalid={!!errors["inspection.date"]}
                                    describedBy={describedBy(
                                        "inspection.date",
                                        "item-inspection-date",
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
                                id="item-conservation"
                                label="Estado de Conservação"
                                error={errors.conservation_state}
                                requiredIndicator
                            >
                                <SelectField
                                    id="item-conservation"
                                    name="conservation_state"
                                    label="Estado de Conservação"
                                    required
                                    value={form.data.conservation_state}
                                    aria-invalid={!!errors.conservation_state}
                                    aria-describedby={describedBy(
                                        "conservation_state",
                                        "item-conservation",
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
                                id="item-evidences"
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
                                    id="item-inspection-description-input"
                                    label="Parecer Técnico"
                                    error={errors["inspection.description"]}
                                >
                                    <SummernoteEditor
                                        id="item-inspection-description"
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
                                            "item-inspection-description-input",
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
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    form.processing
                        ? form.progress
                            ? `Enviando arquivos: ${form.progress.percentage}%`
                            : "Salvando item..."
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
                        title="Salvar item"
                        aria-label={
                            form.processing
                                ? "Salvando item"
                                : mode === "create"
                                  ? "Cadastrar item"
                                  : "Salvar item"
                        }
                        disabled={form.processing || editorFailed}
                    />
                ) : null}
            </FormActions>
        </FormLayout>
    );
}
