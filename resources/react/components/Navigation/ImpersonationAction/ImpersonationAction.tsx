import { router } from "@inertiajs/react";
import { useState } from "react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import Button from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { notify } from "@/components/UI/NotificationToast/notify";
import "./ImpersonationAction.css";

type ImpersonationMode = "read_only" | "full";

const modes: Array<{ value: ImpersonationMode; label: string; description: string }> = [
    {
        value: "read_only",
        label: "Somente leitura",
        description: "Apenas consulta. Cadastros, edições e exclusões ficam bloqueados.",
    },
    {
        value: "full",
        label: "Acesso completo",
        description: "Permite usar o sistema livremente em nome do usuário. As alterações são auditadas com o seu nome.",
    },
];

/**
 * "Entrar como" do administrador superior. O backend valida a permissão e o modo.
 */
export default function ImpersonationAction({
    userId,
    name,
    disabled = false,
    onProcessingChange,
}: {
    userId: number;
    name: string;
    disabled?: boolean;
    onProcessingChange?: (processing: boolean) => void;
}): React.JSX.Element {
    const [open, setOpen] = useState(false);
    const [mode, setMode] = useState<ImpersonationMode>("read_only");
    const [processing, setProcessing] = useState(false);

    function changeProcessing(value: boolean): void {
        setProcessing(value);
        onProcessingChange?.(value);
    }

    function failure(text: string): void {
        notify({ tone: "error", title: "Não foi possível concluir", text });
    }

    function confirm(): void {
        if (processing) return;

        changeProcessing(true);
        router.post(
            `/auth/impersonate/${userId}`,
            { mode },
            {
                onHttpException: () => {
                    failure("Não foi possível entrar como este usuário. Tente novamente.");
                    return false;
                },
                onNetworkError: () => {
                    failure("Falha de conexão. Confira sua conexão e tente novamente.");
                    return false;
                },
                onFinish: () => changeProcessing(false),
            },
        );
    }

    return (
        <>
            <Button
                tone="primary"
                leftIconClass="fa fa-sign-in"
                label="Entrar como"
                title={`Entrar no sistema como ${name}`}
                aria-label={`Entrar no sistema como ${name}`}
                disabled={disabled || processing}
                onClick={() => {
                    setMode("read_only");
                    setOpen(true);
                }}
            />
            <ConfirmActionModal
                open={open}
                title="Entrar como"
                message={
                    <>
                        Escolha como deseja entrar no sistema como <strong>{name}</strong>.
                    </>
                }
                details={
                    <ChoiceGroup
                        id="impersonation-mode"
                        label="Modo de acesso"
                        className="impersonationAction__modes"
                    >
                        {modes.map((option) => (
                            <ChoiceControl
                                key={option.value}
                                type="radio"
                                tone="purple"
                                id={`impersonation-mode-${option.value}`}
                                name="impersonation_mode"
                                checked={mode === option.value}
                                disabled={processing}
                                onChange={(checked) => {
                                    if (checked) setMode(option.value);
                                }}
                                label={
                                    <>
                                        <strong>{option.label}</strong>
                                        <span className="impersonationAction__description">
                                            {option.description}
                                        </span>
                                    </>
                                }
                            />
                        ))}
                    </ChoiceGroup>
                }
                processing={processing}
                confirmLabel="Confirmar acesso"
                processingLabel="Entrando..."
                confirmTone="primary"
                onClose={() => {
                    if (!processing) setOpen(false);
                }}
                onConfirm={confirm}
            />
        </>
    );
}
