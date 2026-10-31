import type { ReactNode } from "react";
import Button from "@/components/UI/Button/Button";
import "./RemovableField.css";

/**
 * Campo com o botão de remover colado à direita, no padrão `input-group-btn`
 * do Ubold. Serve a listas dinâmicas em que cada linha pode ser descartada.
 * Com `removed`, o botão passa a desfazer a remoção marcada.
 */
export default function RemovableField({
    children,
    removeLabel,
    restoreLabel,
    removed = false,
    disabled = false,
    onRemove,
}: {
    children: ReactNode;
    /** Nome acessível do botão, por exemplo "Remover participante 2". */
    removeLabel: string;
    /** Nome acessível quando `removed`, por exemplo "Manter a foto 2". */
    restoreLabel?: string;
    removed?: boolean;
    disabled?: boolean;
    /** Remove a linha ou alterna a marcação de remoção. */
    onRemove: () => void;
}): React.JSX.Element {
    const label = removed && restoreLabel ? restoreLabel : removeLabel;

    return (
        <div className="removableField">
            <div className="removableField__control">{children}</div>
            <Button
                type="button"
                tone={removed ? "white" : "dark-red"}
                iconOnly
                leftIconClass={removed ? "fa fa-undo" : "fa fa-times"}
                className="removableField__button"
                title={label}
                aria-label={label}
                disabled={disabled}
                onClick={onRemove}
            />
        </div>
    );
}
