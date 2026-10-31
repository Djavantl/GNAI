export const teachersBase = "/specialized-educational-support/teachers";

export const teacherShowUrl = (id: number): string => `${teachersBase}/${id}/show`;

export function cpfMask(value: string): string {
    return value
        .replace(/\D/g, "")
        .slice(0, 11)
        .replace(/(\d{3})(\d)/, "$1.$2")
        .replace(/(\d{3})(\d)/, "$1.$2")
        .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
}

export function phoneMask(value: string): string {
    const digits = value.replace(/\D/g, "").slice(0, 11);
    if (digits.length <= 2) return digits ? `(${digits}` : "";

    const number = digits.slice(2);
    const split = digits.length > 10 ? 5 : 4;

    return `(${digits.slice(0, 2)}) ${number.slice(0, split)}${number.length > split ? `-${number.slice(split)}` : ""}`;
}

// Valores aceitos por CreateTeacherData/UpdateTeacherData (in:male,female,other,not_specified),
// com os mesmos rótulos e ordem do formulário Blade de professores.
export const teacherGenderOptions = [
    { value: "not_specified", label: "Não informado" },
    { value: "male", label: "Masculino" },
    { value: "female", label: "Feminino" },
    { value: "other", label: "Outro" },
];
