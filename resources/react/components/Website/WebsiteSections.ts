import type { WebsiteContent, WebsiteSection } from "./Website.types";

export function isWebsiteSectionVisible(section: WebsiteSection, content: WebsiteContent): boolean {
    if (!section.enabled) return false;
    if (section.type === "team") return content.team.length > 0;
    if (section.type === "events") return content.events.length > 0;
    if (section.type === "publications") return content.publications.length > 0;
    if (["pillars", "audience", "features", "guidance", "faq", "resources"].includes(section.type)) {
        return section.items.length > 0;
    }
    return true;
}
