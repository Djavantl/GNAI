import type { GalleryItem } from "./FilteredGallery.types";
export const galleryItems: GalleryItem[] = [
  {
    src: "/assets/react/images/gallery/1.jpg",
    categories: ["webdesign", "illustrator"],
    title: "Imagem da galeria",
    caption: "Captura 1",
  },
  {
    src: "/assets/react/images/gallery/2.jpg",
    categories: ["graphicdesign", "illustrator", "photography"],
    title: "Imagem da galeria",
    caption: "Captura 2",
  },
  {
    src: "/assets/react/images/gallery/3.jpg",
    categories: ["webdesign", "graphicdesign"],
    title: "Imagem da galeria",
    caption: "Captura 3",
  },
  {
    src: "/assets/react/images/gallery/4.jpg",
    categories: ["illustrator", "photography"],
    title: "Imagem da galeria",
    caption: "Captura 4",
  },
  {
    src: "/assets/react/images/gallery/5.jpg",
    categories: ["graphicdesign", "photography"],
    title: "Imagem da galeria",
    caption: "Captura 5",
  },
  {
    src: "/assets/react/images/gallery/6.jpg",
    categories: ["webdesign", "photography"],
    title: "Imagem da galeria",
    caption: "Captura 6",
  },
  {
    src: "/assets/react/images/gallery/7.jpg",
    categories: ["illustrator", "photography", "graphicdesign"],
    title: "Imagem da galeria",
    caption: "Captura 7",
  },
  {
    src: "/assets/react/images/gallery/8.jpg",
    categories: ["graphicdesign", "photography", "webdesign"],
    title: "Imagem da galeria",
    caption: "Captura 8",
  },
  {
    src: "/assets/react/images/gallery/9.jpg",
    categories: ["webdesign", "illustrator"],
    title: "Imagem da galeria",
    caption: "Captura 9",
  },
  {
    src: "/assets/react/images/gallery/10.jpg",
    categories: ["photography", "graphicdesign"],
    title: "Imagem da galeria",
    caption: "Captura 10",
  },
  {
    src: "/assets/react/images/gallery/11.jpg",
    categories: ["graphicdesign", "photography"],
    title: "Imagem da galeria",
    caption: "Captura 11",
  },
  {
    src: "/assets/react/images/gallery/12.jpg",
    categories: ["webdesign", "graphicdesign", "illustrator"],
    title: "Imagem da galeria",
    caption: "Captura 12",
  },
];

export const galleryFilters = [
  { id: "*", label: "Todas" },
  { id: ".webdesign", label: "Design web" },
  { id: ".graphicdesign", label: "Design gráfico" },
  { id: ".illustrator", label: "Ilustração" },
  { id: ".photography", label: "Fotografia" },
];
