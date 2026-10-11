import type { ProductOrderRecord } from "@/components/Tables/ProductOrdersTable/ProductOrdersTable.types";
import type {
  DashboardTwoRevenueSeriesItem,
  DashboardTwoRevenueLegendItem,
  DashboardTwoRevenueProgressItem,
} from "@/components/UI/DashboardTwoRevenueCard/DashboardTwoRevenueCard.types";
export const stats = [
  {
    title: "Receita total",
    value: "50568",
    iconClass: "md md-attach-money text-primary",
  },
  {
    title: "Pedidos",
    value: "1256",
    iconClass: "md md-add-shopping-cart text-pink",
  },
  {
    title: "Lojas",
    value: "18",
    iconClass: "md md-store-mall-directory text-info",
  },
  {
    title: "Vendedores",
    value: "85",
    iconClass: "md md-account-child text-custom",
  },
];
export const revenueLegend: DashboardTwoRevenueLegendItem[] = [
  { label: "Computadores", color: "#36404a" },
  { label: "Tablets", color: "#5d9cec" },
  { label: "Celulares", color: "#bbbbbb" },
];
export const revenueProgress: DashboardTwoRevenueProgressItem[] = [
  { label: "iMacs", value: 80, tone: "primary" },
  { label: "iBooks", value: 50, tone: "pink" },
  { label: "iPhone 5s", value: 70, tone: "info" },
  { label: "iPhone 6", value: 65, tone: "warning" },
  { label: "iPhone 6s", value: 40, tone: "success" },
];
export const revenueSeries: DashboardTwoRevenueSeriesItem[] = [
  { year: "2009", desktop: 10, tablet: 20, mobile: 30 },
  { year: "2010", desktop: 75, tablet: 65, mobile: 30 },
  { year: "2011", desktop: 50, tablet: 40, mobile: 30 },
  { year: "2012", desktop: 75, tablet: 65, mobile: 30 },
  { year: "2013", desktop: 50, tablet: 40, mobile: 30 },
  { year: "2014", desktop: 75, tablet: 65, mobile: 30 },
  { year: "2015", desktop: 90, tablet: 60, mobile: 30 },
];
export const recentOrders: ProductOrderRecord[] = [
  {
    id: "dashboard-0",
    image: "/assets/react/images/products/iphone.jpg",
    date: "08/10/2015",
    number: "UB#160924",
    amount: "$1,256",
  },
  {
    id: "dashboard-1",
    image: "/assets/react/images/products/samsung.jpg",
    date: "08/10/2015",
    number: "UB#160923",
    amount: "$2,562",
  },
  {
    id: "dashboard-2",
    image: "/assets/react/images/products/lumia.jpg",
    date: "08/10/2015",
    number: "UB#160922",
    amount: "$6,458",
  },
  {
    id: "dashboard-3",
    image: "/assets/react/images/products/imac.jpg",
    date: "07/10/2015",
    number: "UB#160921",
    amount: "$1,256",
  },
  {
    id: "dashboard-4",
    image: "/assets/react/images/products/macbook.jpg",
    date: "07/10/2015",
    number: "UB#160920",
    amount: "$7,521",
  },
  {
    id: "dashboard-5",
    image: "/assets/react/images/products/samsung.jpg",
    date: "06/10/2015",
    number: "UB#160920",
    amount: "$7,521",
  },
];
export const popularProducts: ProductOrderRecord[] = [
  {
    id: "dashboard-0",
    image: "/assets/react/images/products/iphone.jpg",
    date: "08/10/2015",
    number: "22",
    amount: "$1,256",
  },
  {
    id: "dashboard-1",
    image: "/assets/react/images/products/samsung.jpg",
    date: "08/10/2015",
    number: "35",
    amount: "$2,562",
  },
  {
    id: "dashboard-2",
    image: "/assets/react/images/products/lumia.jpg",
    date: "08/10/2015",
    number: "20",
    amount: "$6,458",
  },
  {
    id: "dashboard-3",
    image: "/assets/react/images/products/imac.jpg",
    date: "07/10/2015",
    number: "5",
    amount: "$1,256",
  },
  {
    id: "dashboard-4",
    image: "/assets/react/images/products/macbook.jpg",
    date: "07/10/2015",
    number: "10",
    amount: "$7,521",
  },
  {
    id: "dashboard-5",
    image: "/assets/react/images/products/samsung.jpg",
    date: "06/10/2015",
    number: "7",
    amount: "$7,521",
  },
];

export const dashboardTables = [
  {
    title: "Pedidos recentes",
    records: recentOrders,
    variant: "recent" as const,
  },
  {
    title: "Produtos populares",
    records: popularProducts,
    variant: "popular" as const,
  },
];
