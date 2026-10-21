export type LoanOptionMap = Record<string, string>;

export type LoanableItem = {
    id: number;
    name: string;
    asset_code: string;
    is_digital: boolean;
    quantity_available: number | null;
};

export type LoanRecord = {
    id: number;
    loanable_id: number;
    loanable_type: string;
    loanable_type_label: string;
    loanable_icon: string;
    loanable: { name: string; asset_code: string | null };
    student: { name: string; registration: string } | null;
    professional: { name: string; registration: string } | null;
    user: { name: string };
    loan_date: string | null;
    loan_date_label: string;
    due_date: string | null;
    due_date_label: string;
    return_date_label: string | null;
    status: string | null;
    status_label: string;
    status_color: string;
    observation: string | null;
    is_active: boolean;
    is_returned: boolean;
    is_overdue: boolean;
};

export type LoanFormProps = {
    students: LoanOptionMap;
    professionals: LoanOptionMap;
    authUser: { id: number; name: string };
    can: { submit: boolean };
};

export type LoanPaginator = {
    data: LoanRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};
