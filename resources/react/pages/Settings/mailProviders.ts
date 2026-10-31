export const OTHER_OPTION = "other";

export interface MailProvider {
    key: string;
    label: string;
    host: string;
    port: number;
    encryption: "tls" | "ssl";
    passwordHint?: string;
}

// Atalhos de preenchimento: o backend continua recebendo apenas host, porta e segurança.
export const MAIL_PROVIDERS: MailProvider[] = [
    {
        key: "gmail",
        label: "Gmail / Google Workspace",
        host: "smtp.gmail.com",
        port: 587,
        encryption: "tls",
        passwordHint: "No Gmail, use uma senha de app (exige a verificação em duas etapas ativa).",
    },
    {
        key: "outlook",
        label: "Outlook.com / Hotmail",
        host: "smtp-mail.outlook.com",
        port: 587,
        encryption: "tls",
        passwordHint: "A Microsoft vem restringindo o envio SMTP com senha; se o teste falhar, confira se a conta permite SMTP autenticado.",
    },
    {
        key: "office365",
        label: "Microsoft 365",
        host: "smtp.office365.com",
        port: 587,
        encryption: "tls",
        passwordHint: "No Microsoft 365, o SMTP autenticado precisa estar habilitado para a conta pelo administrador.",
    },
    {
        key: "yahoo",
        label: "Yahoo Mail",
        host: "smtp.mail.yahoo.com",
        port: 465,
        encryption: "ssl",
        passwordHint: "No Yahoo, gere uma senha de app nas configurações de segurança da conta.",
    },
    {
        key: "zoho",
        label: "Zoho Mail",
        host: "smtp.zoho.com",
        port: 465,
        encryption: "ssl",
    },
    {
        key: "hostinger",
        label: "Hostinger",
        host: "smtp.hostinger.com",
        port: 465,
        encryption: "ssl",
    },
    {
        key: "locaweb",
        label: "Locaweb",
        host: "email-ssl.com.br",
        port: 465,
        encryption: "ssl",
    },
];

export const MAIL_PORTS: Array<{
    value: string;
    label: string;
    encryption?: "tls" | "ssl";
}> = [
    { value: "587", label: "587 (STARTTLS)", encryption: "tls" },
    { value: "465", label: "465 (SSL/TLS)", encryption: "ssl" },
    { value: "25", label: "25" },
    { value: "2525", label: "2525" },
];

export function providerChoice(host: string): string {
    if (!host) return "";

    return MAIL_PROVIDERS.find(
        (provider) => provider.host === host.trim().toLowerCase(),
    )?.key ?? OTHER_OPTION;
}

export function portChoice(port: string): string {
    if (!port) return "";

    return MAIL_PORTS.some((option) => option.value === port) ? port : OTHER_OPTION;
}
