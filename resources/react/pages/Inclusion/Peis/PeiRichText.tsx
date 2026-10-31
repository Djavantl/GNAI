/** Texto longo já sanitizado pelo backend (mesmo contrato das caixas de texto do Blade). */
export default function PeiRichText({
    html,
}: {
    html: string;
}): React.JSX.Element {
    return (
        <div
            className="studentsPage__richText peiShow__rich"
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
