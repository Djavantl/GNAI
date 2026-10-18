import { useMemo } from "react";
import ImageLightbox from "@/components/UI/ImageLightbox/ImageLightbox";
import OwlCarousel from "@/components/UI/OwlCarousel/OwlCarousel";
import type { OwlCarouselOptions } from "@/components/UI/OwlCarousel/OwlCarousel.types";
import type { InspectionEvidence } from "./InspectionEvidenceCarousel.types";
import "./InspectionEvidenceCarousel.css";

const evidenceIconClasses: Record<string, string> = {
    "fa-file-pdf": "fa fa-file-pdf-o",
    "fa-file-word": "fa fa-file-word-o",
    "fa-file-powerpoint": "fa fa-file-powerpoint-o",
    "fa-file-excel": "fa fa-file-excel-o",
};

export default function InspectionEvidenceCarousel({
    evidences,
}: {
    evidences: InspectionEvidence[];
}): React.JSX.Element {
    const hasMultipleEvidences = evidences.length > 1;
    const tabletItems = Math.min(evidences.length, 2);
    const desktopItems = Math.min(evidences.length, 3);
    const options = useMemo<OwlCarouselOptions>(
        () => ({
            loop: hasMultipleEvidences,
            autoplay: hasMultipleEvidences,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            items: desktopItems,
            margin: 0,
            nav: false,
            dots: hasMultipleEvidences,
            responsive: {
                0: { items: 1 },
                600: { items: tabletItems },
                1000: { items: desktopItems },
            },
        }),
        [desktopItems, hasMultipleEvidences, tabletItems],
    );

    return (
        <ImageLightbox
            className="inspectionEvidenceCarousel"
            deduplicateByHref
        >
            <OwlCarousel
                options={options}
                theme
                label="Evidências da vistoria"
            >
                {evidences.map((evidence, index) => (
                    <div className="item" key={evidence.id}>
                        <a
                            className="inspectionEvidenceCarousel__slide"
                            href={evidence.url}
                            target={evidence.isImage ? undefined : "_blank"}
                            rel={
                                evidence.isImage
                                    ? undefined
                                    : "noopener noreferrer"
                            }
                            data-lightbox={
                                evidence.isImage ? "evidence" : undefined
                            }
                            title={evidence.isImage ? evidence.name : undefined}
                            aria-label={`Abrir evidência ${evidence.name}`}
                        >
                            {evidence.isImage ? (
                                <img
                                    className="inspectionEvidenceCarousel__image"
                                    src={evidence.url}
                                    alt={`Evidência ${evidence.name}`}
                                    loading={index === 0 ? "eager" : "lazy"}
                                />
                            ) : (
                                <span className="inspectionEvidenceCarousel__document">
                                    <i
                                        className={
                                            evidenceIconClasses[
                                                evidence.iconClass
                                            ] ?? "fa fa-file-o"
                                        }
                                        aria-hidden="true"
                                    />
                                    <span>{evidence.name}</span>
                                </span>
                            )}
                            <span className="inspectionEvidenceCarousel__caption">
                                {evidence.name}
                            </span>
                        </a>
                    </div>
                ))}
            </OwlCarousel>
        </ImageLightbox>
    );
}
