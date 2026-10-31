import { joinClassNames } from '@/utils/joinClassNames';
import './RichTextContent.css';

/** Conteúdo HTML já sanitizado pelo RichTextSanitizer no servidor. */
export default function RichTextContent({ html, className }: {
    html: string;
    className?: string;
}): React.JSX.Element {
    return (
        <div
            className={joinClassNames('richTextContent', className)}
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
