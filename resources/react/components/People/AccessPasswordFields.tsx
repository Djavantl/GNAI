import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import TextInput from "@/components/Forms/TextInput/TextInput";

export type AccessPasswordField = "password" | "password_confirmation";

interface AccessPasswordFieldsProps {
    idPrefix: string;
    password: string;
    confirmation: string;
    error?: string;
    onChange: (field: AccessPasswordField, value: string) => void;
}

/**
 * Senha inicial opcional do cadastro. Sem senha, o backend mantém o envio do
 * link de definição de senha por e-mail.
 */
export default function AccessPasswordFields({
    idPrefix,
    password,
    confirmation,
    error,
    onChange,
}: AccessPasswordFieldsProps): React.JSX.Element {
    const passwordId = `${idPrefix}-password`;
    const confirmationId = `${idPrefix}-password-confirmation`;
    const passwordDescription = [
        `${passwordId}-hint`,
        error ? `${passwordId}-error` : null,
    ]
        .filter(Boolean)
        .join(" ");

    return (
        <FormGrid>
            <FormField
                id={passwordId}
                label="Senha de acesso"
                hint="Mínimo de 8 caracteres, com letras e números. Deixe em branco para enviar por e-mail o link de definição de senha."
                error={error}
            >
                <TextInput
                    id={passwordId}
                    name="password"
                    type="password"
                    autoComplete="new-password"
                    minLength={8}
                    maxLength={255}
                    value={password}
                    invalid={!!error}
                    aria-describedby={passwordDescription}
                    onChange={(event) =>
                        onChange("password", event.target.value)
                    }
                />
            </FormField>
            <FormField
                id={confirmationId}
                label="Confirmar senha"
                hint="Repita a senha informada."
            >
                <TextInput
                    id={confirmationId}
                    name="password_confirmation"
                    type="password"
                    autoComplete="new-password"
                    maxLength={255}
                    value={confirmation}
                    invalid={!!error}
                    aria-describedby={`${confirmationId}-hint`}
                    onChange={(event) =>
                        onChange("password_confirmation", event.target.value)
                    }
                />
            </FormField>
        </FormGrid>
    );
}
