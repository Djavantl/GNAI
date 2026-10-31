export type SettingsOptions = Record<string, string>;

export interface MailSettingsProps {
    configured: boolean;
    mail: {
        transport: string;
        host: string | null;
        port: number | null;
        encryption: string;
        username: string | null;
        from_address: string;
        from_name: string;
        has_password: boolean;
    };
    transports: SettingsOptions;
    encryptions: SettingsOptions;
    canUpdate: boolean;
}

export interface BackupSettingsValues {
    retention_mode: string;
    retention_value: number;
    times: string[];
}

export interface BackupSettingsProps {
    backup: BackupSettingsValues;
    defaults: BackupSettingsValues;
    retentionModes: SettingsOptions;
    timezone: string;
    canUpdate: boolean;
}
