import AccessibilityFeatureForm from "./AccessibilityFeatureForm";
import type { AccessibilityFeatureRecord } from "./AccessibilityFeature.types";

export default function InclusiveRadarAccessibilityFeaturesEditPage({
    accessibilityFeature,
    can,
}: {
    accessibilityFeature: AccessibilityFeatureRecord;
    can: { update: boolean };
}): React.JSX.Element {
    return (
        <AccessibilityFeatureForm
            feature={accessibilityFeature}
            canSubmit={can.update}
        />
    );
}
