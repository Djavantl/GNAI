export interface SalesAnalyticsLegendItem {
  label: string;
  color: string;
}

export interface SalesAnalyticsDatum {
  year: string;
  desktop: number;
  tablet: number;
  mobile: number;
}

export interface SalesAnalyticsData {
  title: string;
  legend: SalesAnalyticsLegendItem[];
  series: SalesAnalyticsDatum[];
}

export const salesAnalyticsData: SalesAnalyticsData = {
  title: 'Análise de vendas',
  legend: [
    { label: 'Desktops', color: '#5fbeaa' },
    { label: 'Tablets', color: '#5d9cec' },
    { label: 'Celulares', color: '#ebeff2' }
  ],
  series: [
    { year: '2005', desktop: 45, tablet: 180, mobile: 100 },
    { year: '2006', desktop: 75, tablet: 65, mobile: 80 },
    { year: '2007', desktop: 100, tablet: 90, mobile: 56 },
    { year: '2008', desktop: 75, tablet: 65, mobile: 89 },
    { year: '2009', desktop: 100, tablet: 90, mobile: 120 },
    { year: '2010', desktop: 75, tablet: 65, mobile: 110 },
    { year: '2011', desktop: 50, tablet: 40, mobile: 85 },
    { year: '2012', desktop: 75, tablet: 65, mobile: 52 },
    { year: '2013', desktop: 50, tablet: 40, mobile: 77 },
    { year: '2014', desktop: 75, tablet: 65, mobile: 90 },
    { year: '2015', desktop: 100, tablet: 90, mobile: 130 }
  ]
};
