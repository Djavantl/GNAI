import DetailField from "@/components/UI/DetailField/DetailField";
import type { WaitlistRecord } from "./Waitlist.types";

type Props = Pick<
    WaitlistRecord,
    "waitlistable_type" | "waitlistable_id" | "item_name" | "asset_code"
>;

export default function WaitlistResourceDetails({
    waitlistable_type,
    waitlistable_id,
    item_name,
    asset_code,
}: Props): React.JSX.Element {
    const resourcePath =
        waitlistable_type === "assistive_technology"
            ? "assistive-technologies"
            : "accessible-educational-materials";

    return (
        <dl className="waitlistShow__grid">
            <DetailField label="Recurso Solicitado">
                <a
                    href={`/inclusive-radar/${resourcePath}/${waitlistable_id}`}
                    target="_blank"
                    rel="noreferrer"
                >
                    {item_name}
                </a>
            </DetailField>
            <DetailField label="Patrimônio / Tombamento">
                {asset_code || "Não se aplica"}
            </DetailField>
        </dl>
    );
}
