import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import { settingsBase, SettingsLayout } from "./SettingsPage";
import type { BackupSettingsProps } from "./Settings.types";

const MAX_TIMES = 6;

type FormData = {
    retention_mode: string;
    retention_value: string;
    times: string[];
};

export default function SettingsBackup({
    backup,
    defaults,
    retentionModes,
    timezone,
    canUpdate,
}: BackupSettingsProps): React.JSX.Element {
    const form = useForm<FormData>({
        retention_mode: backup.retention_mode,
        retention_value: String(backup.retention_value),
        times: backup.times.length ? backup.times : [""],
    });
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const errors = form.errors as Record<string, string | undefined>;
    const byDays = form.data.retention_mode === "days";
    const timeError = (index: number): string | undefined =>
        errors[`times.${index}`];
    const describedBy = (id: string, error?: string, hint = false): string | undefined =>
        [hint ? `${id}-hint` : null, error ? `${id}-error` : null]
            .filter(Boolean)
            .join(" ") || undefined;
    const updateTimes = (times: string[]): void => {
        // Os erros dos horários são indexados; ao mudar a lista, deixam de corresponder.
        form.setData("times", times);
        form.clearErrors();
    };

    return (
        <FormLayout
            ref={element}
            aria-label="Configurações de backup"
            aria-busy={form.processing}
            onSubmit={(event) => {
                event.preventDefault();
                if (!canUpdate || busy.current || form.processing) return;
                busy.current = true;
                form.put(`${settingsBase}/backups`, {
                    preserveScroll: true,
                    onError: () =>
                        requestAnimationFrame(() =>
                            element.current
                                ?.querySelector<HTMLElement>(
                                    '[aria-invalid="true"]',
                                )
                                ?.focus(),
                        ),
                    onHttpException: () => {
                        notify({
                            tone: "error",
                            title: "Não foi possível concluir",
                            text: "Não foi possível concluir a operação. Atualize a página e tente novamente.",
                        });
                        return false;
                    },
                    onNetworkError: () => {
                        notify({
                            tone: "error",
                            title: "Falha de conexão",
                            text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                        });
                        return false;
                    },
                    onFinish: () => {
                        busy.current = false;
                    },
                });
            }}
        >
            <CardBox>
                <fieldset disabled={!canUpdate || form.processing}>
                    <FormSection
                        id="backup-retention"
                        title="Retenção"
                        variant="inline"
                        iconClass="fa fa-history"
                    >
                        <FormGrid>
                            <FormField
                                id="backup-retention-mode"
                                label="Como manter os backups"
                                error={form.errors.retention_mode}
                                requiredIndicator
                            >
                                <SelectField
                                    id="backup-retention-mode"
                                    name="retention_mode"
                                    label="Como manter os backups"
                                    required
                                    disabled={!canUpdate || form.processing}
                                    value={form.data.retention_mode}
                                    aria-invalid={!!form.errors.retention_mode}
                                    aria-describedby={describedBy(
                                        "backup-retention-mode",
                                        form.errors.retention_mode,
                                    )}
                                    options={Object.entries(retentionModes).map(
                                        ([value, label]) => ({ value, label }),
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "retention_mode",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                            <FormField
                                id="backup-retention-value"
                                label={
                                    byDays
                                        ? "Dias de retenção"
                                        : "Quantidade de backups mantidos"
                                }
                                hint={
                                    byDays
                                        ? "Backups com mais dias que o informado são removidos automaticamente; o mais recente é sempre mantido. Entre 1 e 365."
                                        : `Os backups mais antigos além desta quantidade são removidos automaticamente. Entre 1 e 365; padrão: ${defaults.retention_value}.`
                                }
                                error={form.errors.retention_value}
                                requiredIndicator
                            >
                                <TextInput
                                    id="backup-retention-value"
                                    name="retention_value"
                                    type="number"
                                    required
                                    min={1}
                                    max={365}
                                    inputMode="numeric"
                                    value={form.data.retention_value}
                                    invalid={!!form.errors.retention_value}
                                    aria-describedby={describedBy(
                                        "backup-retention-value",
                                        form.errors.retention_value,
                                        true,
                                    )}
                                    onChange={(event) =>
                                        form.setData(
                                            "retention_value",
                                            event.target.value,
                                        )
                                    }
                                />
                            </FormField>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="backup-schedule"
                        title="Backup automático"
                        variant="inline"
                        iconClass="fa fa-clock-o"
                        description={`Horários diários de execução, no fuso horário do sistema (${timezone}). Padrão: ${defaults.times.join(", ")}.`}
                    >
                        <div className="settingsPage__times">
                            {form.data.times.map((time, index) => (
                                <div
                                    className="settingsPage__time"
                                    key={index}
                                >
                                    <FormField
                                        id={`backup-time-${index}`}
                                        label={`Horário ${index + 1}`}
                                        error={timeError(index)}
                                        requiredIndicator
                                    >
                                        <TextInput
                                            id={`backup-time-${index}`}
                                            name={`times[${index}]`}
                                            type="time"
                                            required
                                            value={time}
                                            invalid={!!timeError(index)}
                                            aria-describedby={describedBy(
                                                `backup-time-${index}`,
                                                timeError(index),
                                            )}
                                            onChange={(event) =>
                                                updateTimes(
                                                    form.data.times.map(
                                                        (current, position) =>
                                                            position === index
                                                                ? event.target.value
                                                                : current,
                                                    ),
                                                )
                                            }
                                        />
                                    </FormField>
                                    {canUpdate ? (
                                        <Button
                                            type="button"
                                            tone="cancel"
                                            iconOnly
                                            leftIconClass="fa fa-trash"
                                            className="settingsPage__timeRemove"
                                            title={`Remover horário ${index + 1}`}
                                            aria-label={`Remover horário ${index + 1}`}
                                            disabled={
                                                form.processing ||
                                                form.data.times.length === 1
                                            }
                                            onClick={() =>
                                                updateTimes(
                                                    form.data.times.filter(
                                                        (_, position) =>
                                                            position !== index,
                                                    ),
                                                )
                                            }
                                        />
                                    ) : null}
                                </div>
                            ))}
                        </div>
                        {errors.times ? (
                            <p className="formField__error">{errors.times}</p>
                        ) : null}
                        {canUpdate ? (
                            <Button
                                type="button"
                                tone="dark-blue"
                                leftIconClass="fa fa-plus"
                                label="Adicionar horário"
                                title="Adicionar horário de backup"
                                disabled={
                                    form.processing ||
                                    form.data.times.length >= MAX_TIMES
                                }
                                onClick={() =>
                                    updateTimes([...form.data.times, ""])
                                }
                            />
                        ) : null}
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    form.processing
                        ? "Salvando configurações de backup..."
                        : ""
                }
            >
                <Button
                    href="/auth/dashboard"
                    tone="cancel"
                    label="Cancelar"
                    title="Cancelar e voltar ao início"
                    aria-label="Cancelar e voltar ao início"
                    disabled={form.processing}
                />
                {canUpdate ? (
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar configurações de backup"
                        aria-label={
                            form.processing
                                ? "Salvando configurações de backup"
                                : "Salvar configurações de backup"
                        }
                        disabled={form.processing}
                    />
                ) : null}
            </FormActions>
        </FormLayout>
    );
}

SettingsBackup.layout = SettingsLayout;
