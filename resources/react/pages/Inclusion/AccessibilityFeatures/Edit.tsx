import AccessibilityFeatureForm from "./AccessibilityFeatureForm";
import type { AccessibilityFeatureRecord } from "./AccessibilityFeature.types";

export default function InclusionAccessibilityFeaturesEditPage({
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
