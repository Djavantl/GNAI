import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import type { PillarCardAppearance } from "@/components/Website/Website.types";
import type { BuilderLinkOption } from "./BuilderLinkField";
import BuilderHeroAppearance from "./BuilderHeroAppearance";
import BuilderAboutEditor from "./BuilderAboutEditor";
import BuilderHeroSlides from "./BuilderHeroSlides";
import BuilderPillarsEditor from "./BuilderPillarsEditor";
import type { BuilderEditTarget, BuilderSection } from "./websiteBuilder";

type Props = {
    pillarCardTemplate: PillarCardAppearance;
    guidanceStepTemplate: PillarCardAppearance;
    linkOptions: BuilderLinkOption[];
    editTarget: BuilderEditTarget | null;
    onEditTarget: (target: BuilderEditTarget) => void;
    section: BuilderSection;
    index: number;
    errors: Record<string, string | undefined>;
    disabled: boolean;
    onChange: (patch: Partial<BuilderSection>) => void;
    onImageError: (message: string) => void;
    onEditorError: () => void;
};

/** Campos editáveis da seção selecionada, conforme o tipo do bloco. */
export default function BuilderSectionEditor({
    section,
    pillarCardTemplate,
    guidanceStepTemplate,
    linkOptions,
    editTarget,
    onEditTarget,
    index,
    errors,
    disabled,
    onChange,
    onImageError,
    onEditorError,
}: Props): React.JSX.Element {
    if (section.type === "hero") {
        return (
            <FormGrid>
                <BuilderHeroAppearance
                    section={section}
                    linkOptions={linkOptions}
                    index={index}
                    disabled={disabled}
                    errors={errors}
                    onChange={onChange}
                    onImageError={onImageError}
                />
                <BuilderHeroSlides
                    section={section}
                    index={index}
                    disabled={disabled}
                    errors={errors}
                    onChange={onChange}
                    onImageError={onImageError}
                />
            </FormGrid>
        );
    }

    if (section.type === "about") {
        return (
            <BuilderAboutEditor
                linkOptions={linkOptions}
                section={section}
                index={index}
                disabled={disabled}
                errors={errors}
                onChange={onChange}
                onImageError={onImageError}
                onEditorError={onEditorError}
            />
        );
    }

    return (
        <BuilderPillarsEditor
            section={section}
            index={index}
            disabled={disabled}
            errors={errors}
            template={section.type === "guidance" ? guidanceStepTemplate : pillarCardTemplate}
            linkOptions={linkOptions}
            editTarget={editTarget}
            onEditTarget={onEditTarget}
            onChange={onChange}
        />
    );
}
