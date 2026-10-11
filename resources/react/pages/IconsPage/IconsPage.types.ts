export interface IconGalleryItem {
  className: string;
  label: string;
}

export interface IconGallerySection {
  title: string | null;
  badge: string | null;
  descriptionHtml: string | null;
  icons: IconGalleryItem[];
}

export interface IconsPageData {
  key: string;
  href: string;
  title: string;
  breadcrumb: string;
  sections: IconGallerySection[];
}
