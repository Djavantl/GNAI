import { lazy, type ReactElement } from 'react';
import type { LayoutDefinition } from '@/pages/Layouts/Layouts.types';
const DashboardOne = lazy(() => import('@/pages/DashboardOne/DashboardOne'));
const DashboardTwo = lazy(() => import('@/pages/DashboardTwo/DashboardTwo'));
const DashboardThree = lazy(() => import('@/pages/DashboardThree/DashboardThree'));
const Buttons = lazy(() => import('@/pages/Buttons/Buttons'));
const Panels = lazy(() => import('@/pages/Panels/Panels'));
const Portlets = lazy(() => import('@/pages/Portlets/Portlets'));
const CheckboxRadio = lazy(() => import('@/pages/CheckboxRadio/CheckboxRadio'));
const TabsPage = lazy(() => import('@/pages/TabsPage/TabsPage'));
const Modals = lazy(() => import('@/pages/Modals/Modals'));
const ProgressBars = lazy(() => import('@/pages/ProgressBars/ProgressBars'));
const Notifications = lazy(() => import('@/pages/Notifications/Notifications'));
const Images = lazy(() => import('@/pages/Images/Images'));
const Carousels = lazy(() => import('@/pages/Carousels/Carousels'));
const BootstrapElements = lazy(() => import('@/pages/BootstrapElements/BootstrapElements'));
const Typography = lazy(() => import('@/pages/Typography/Typography'));
const ComponentsGrid = lazy(() => import('@/pages/ComponentsGrid/ComponentsGrid'));
const ComponentsWidgets = lazy(() => import('@/pages/ComponentsWidgets/ComponentsWidgets'));
const ComponentsNestable = lazy(() => import('@/pages/ComponentsNestable/ComponentsNestable'));
const ComponentsRangeSliders = lazy(() => import('@/pages/ComponentsRangeSliders/ComponentsRangeSliders'));
const ComponentsAnimation = lazy(() => import('@/pages/ComponentsAnimation/ComponentsAnimation'));
const ComponentsSweetAlert = lazy(() => import('@/pages/ComponentsSweetAlert/ComponentsSweetAlert'));
const FormElements = lazy(() => import('@/pages/FormElements/FormElements'));
const FormAdvanced = lazy(() => import('@/pages/FormAdvanced/FormAdvanced'));
const FormValidation = lazy(() => import('@/pages/FormValidation/FormValidation'));
const FormPickers = lazy(() => import('@/pages/FormPickers/FormPickers'));
const FormWizardPage = lazy(() => import('@/pages/FormWizardPage/FormWizardPage'));
const FormMasks = lazy(() => import('@/pages/FormMasks/FormMasks'));
const SummernotePage = lazy(() => import('@/pages/SummernotePage/SummernotePage'));
const WysiwigPage = lazy(() => import('@/pages/WysiwigPage/WysiwigPage'));
const UploadsPage = lazy(() => import('@/pages/UploadsPage/UploadsPage'));
const XEditablePage = lazy(() => import('@/pages/XEditablePage/XEditablePage'));
const ImageCropPage = lazy(() => import('@/pages/ImageCropPage/ImageCropPage'));
const Applications = lazy(() => import('@/pages/Applications/Applications'));
const Crm = lazy(() => import('@/pages/Crm/Crm'));
const Ecommerce = lazy(() => import('@/pages/Ecommerce/Ecommerce'));
const Extras = lazy(() => import('@/pages/Extras/Extras'));
const Pages = lazy(() => import('@/pages/Pages/Pages'));
const Layouts = lazy(() => import('@/pages/Layouts/Layouts'));
const IconsPage = lazy(() => import('@/pages/IconsPage/IconsPage'));
const ChartsPage = lazy(() => import('@/pages/ChartsPage/ChartsPage'));
const TablesPage = lazy(() => import('@/pages/TablesPage/TablesPage'));
const MapsPage = lazy(() => import('@/pages/MapsPage/MapsPage'));
import { applications } from '@/pages/Applications/Applications.data';
import { crmPages } from '@/pages/Crm/Crm.data';
import { ecommercePages } from '@/pages/Ecommerce/Ecommerce.data';
import { extras } from '@/pages/Extras/Extras.data';
import { pages } from '@/pages/Pages/Pages.data';
import { layouts } from '@/pages/Layouts/Layouts.data';

export interface CatalogRoute {
  key: string;
  content: ReactElement;
  standalone?: boolean;
  layout?: LayoutDefinition;
}

export function resolveRoute(filename: string): CatalogRoute | undefined {
  switch (filename) {
    case 'index.html': return { key: 'dashboard-1', content: <DashboardOne /> };
    case 'dashboard_2.html': return { key: 'dashboard-2', content: <DashboardTwo /> };
    case 'dashboard_3.html': return { key: 'dashboard-3', content: <DashboardThree /> };
    case 'ui-buttons.html': return { key: 'ui-buttons', content: <Buttons /> };
    case 'ui-panels.html': return { key: 'ui-panels', content: <Panels /> };
    case 'ui-portlets.html': return { key: 'ui-portlets', content: <Portlets /> };
    case 'ui-checkbox-radio.html': return { key: 'ui-checkbox-radio', content: <CheckboxRadio /> };
    case 'ui-tabs.html': return { key: 'ui-tabs', content: <TabsPage /> };
    case 'ui-modals.html': return { key: 'ui-modals', content: <Modals /> };
    case 'ui-progressbars.html': return { key: 'ui-progressbars', content: <ProgressBars /> };
    case 'ui-notification.html': return { key: 'ui-notification', content: <Notifications /> };
    case 'ui-images.html': return { key: 'ui-images', content: <Images /> };
    case 'ui-carousel.html': return { key: 'ui-carousel', content: <Carousels /> };
    case 'ui-bootstrap.html': return { key: 'ui-bootstrap', content: <BootstrapElements /> };
    case 'ui-typography.html': return { key: 'ui-typography', content: <Typography /> };
    case 'components-grid.html': return { key: 'components-grid', content: <ComponentsGrid /> };
    case 'components-widgets.html': return { key: 'components-widgets', content: <ComponentsWidgets /> };
    case 'components-nestable-list.html': return { key: 'components-nestable', content: <ComponentsNestable /> };
    case 'components-range-sliders.html': return { key: 'components-range-sliders', content: <ComponentsRangeSliders /> };
    case 'components-animation.html': return { key: 'components-animation', content: <ComponentsAnimation /> };
    case 'components-sweet-alert.html': return { key: 'components-sweet-alert', content: <ComponentsSweetAlert /> };
    case 'form-elements.html': return { key: 'form-elements', content: <FormElements /> };
    case 'form-advanced.html': return { key: 'form-advanced', content: <FormAdvanced /> };
    case 'form-validation.html': return { key: 'form-validation', content: <FormValidation /> };
    case 'form-pickers.html': return { key: 'form-pickers', content: <FormPickers /> };
    case 'form-wizard.html': return { key: 'form-wizard', content: <FormWizardPage /> };
    case 'form-mask.html': return { key: 'form-mask', content: <FormMasks /> };
    case 'form-summernote.html': return { key: 'form-summernote', content: <SummernotePage /> };
    case 'form-wysiwig.html': return { key: 'form-wysiwig', content: <WysiwigPage /> };
    case 'form-uploads.html': return { key: 'form-uploads', content: <UploadsPage /> };
    case 'form-xeditable.html': return { key: 'form-xeditable', content: <XEditablePage /> };
    case 'form-image-crop.html': return { key: 'form-image-crop', content: <ImageCropPage /> };
  }
  if (Object.hasOwn(applications, filename)) return { key: applications[filename].key, content: <Applications page={applications[filename]} /> };
  if (Object.hasOwn(crmPages, filename)) return { key: crmPages[filename].key, content: <Crm page={crmPages[filename]} /> };
  if (Object.hasOwn(ecommercePages, filename)) return { key: ecommercePages[filename].key, content: <Ecommerce page={ecommercePages[filename]} /> };
  if (Object.hasOwn(extras, filename)) return { key: extras[filename].key, content: <Extras page={extras[filename]} />, standalone: extras[filename].standalone };
  if (Object.hasOwn(pages, filename)) return { key: pages[filename].key, content: <Pages page={pages[filename]} />, standalone: pages[filename].standalone };
  if (Object.hasOwn(layouts, filename)) return { key: layouts[filename].key, content: <Layouts page={layouts[filename]} />, layout: layouts[filename] };
  const key = filename.replace(/\.html$/, '');
  if (/^icons-(glyphicons|materialdesign|ionicons|fontawesome|themifyicon|simple-line|weather)$/.test(key)) {
    return { key, content: <IconsPage pageKey={key} /> };
  }
  if (/^chart-(flot|morris|chartjs|c3|chartist|nvd3|sparkline|peity|radial|other)$/.test(key)) {
    return { key, content: <ChartsPage variant={key.slice(6)} /> };
  }
  if (/^tables-(basic|datatable|editable|responsive|foo-tables|bootstrap)$/.test(key)) {
    return { key, content: <TablesPage variant={key.slice(7)} /> };
  }
  if (key === 'map-google' || key === 'map-vector') {
    return { key, content: <MapsPage variant={key === 'map-google' ? 'google' : 'vector'} /> };
  }
  return undefined;
}
