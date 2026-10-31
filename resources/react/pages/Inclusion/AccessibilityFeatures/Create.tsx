import AccessibilityFeatureForm from "./AccessibilityFeatureForm";

export default function InclusionAccessibilityFeaturesCreatePage({
    can,
}: {
    can: { store: boolean };
}): React.JSX.Element {
    return <AccessibilityFeatureForm canSubmit={can.store} />;
}
