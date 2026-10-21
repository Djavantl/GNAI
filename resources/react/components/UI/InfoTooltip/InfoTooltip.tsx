import Tooltip from "@/components/UI/Tooltip/Tooltip";
import "./InfoTooltip.css";

export default function InfoTooltip({
    text,
    label = "Mais informações",
}: {
    text: string;
    label?: string;
}): React.JSX.Element {
    return (
        <Tooltip text={text}>
            <button
                type="button"
                className="infoTooltip__trigger"
                title={label}
                aria-label={label}
            >
                <i className="fa fa-info-circle" aria-hidden="true" />
            </button>
        </Tooltip>
    );
}
