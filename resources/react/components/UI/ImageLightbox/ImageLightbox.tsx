import { useEffect, useRef, type ReactNode } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import type {
  LightboxPopupContext,
  LightboxRuntime,
} from "./ImageLightbox.types";
import "./ImageLightbox.plugin.css";
export default function ImageLightbox({
  children,
  className,
  deduplicateByHref = false,
}: {
  children: ReactNode;
  className?: string;
  deduplicateByHref?: boolean;
}): React.JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  useEffect(() => {
    let disposed = false;
    let cleanup: (() => void) | undefined;
    const element = host.current;
    const initialize = async (): Promise<void> => {
      await loadExternalScript("/assets/react/js/jquery.min.js");
      await loadExternalScript(
        "/assets/react/plugins/magnific-popup/dist/jquery.magnific-popup.min.js",
      );
      if (disposed || !element) return;
      const runtime = (window as unknown as { jQuery: LightboxRuntime }).jQuery;
      const links = runtime(element);
      links.magnificPopup({
        delegate: "[data-lightbox]",
        type: "image",
        closeOnContentClick: true,
        mainClass: "mfp-fade",
        closeMarkup:
          '<button title="Fechar (Esc)" type="button" class="mfp-close">×</button>',
        tLoading: "Carregando...",
        image: { tError: "Não foi possível carregar a imagem." },
        callbacks: {
          beforeOpen(this: LightboxPopupContext): void {
            if (!deduplicateByHref) return;
            const selectedHref = this.st.el?.[0]?.getAttribute("href");
            const uniqueHrefs = new Set<string>();
            this.items = this.items.filter((item) => {
              const href = item.getAttribute("href");
              if (!href || uniqueHrefs.has(href)) return false;
              uniqueHrefs.add(href);
              return true;
            });
            const selectedIndex = this.items.findIndex(
              (item) => item.getAttribute("href") === selectedHref,
            );
            this.index = Math.max(0, selectedIndex);
          },
        },
        gallery: {
          enabled: true,
          navigateByImgClick: true,
          preload: [0, 1],
          tPrev: "Anterior (seta esquerda)",
          tNext: "Próxima (seta direita)",
          tCounter: "%curr% de %total%",
        },
      });
      cleanup = () => {
        const popup = runtime.magnificPopup.instance;
        if (
          popup.isOpen &&
          popup.currItem?.el?.[0] &&
          element.contains(popup.currItem.el[0])
        )
          popup.close();
        links.off(".magnificPopup").removeData("magnificPopup");
      };
    };
    void initialize().catch(console.error);
    return () => {
      disposed = true;
      cleanup?.();
    };
  }, [deduplicateByHref]);
  return (
    <div ref={host} className={className}>
      {children}
    </div>
  );
}
