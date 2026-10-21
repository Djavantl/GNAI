import AccessibilityFeatureForm from "./AccessibilityFeatureForm";

export default function InclusiveRadarAccessibilityFeaturesCreatePage({
    can,
}: {
    can: { store: boolean };
}): React.JSX.Element {
    return <AccessibilityFeatureForm canSubmit={can.store} />;
}
