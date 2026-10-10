export interface TotalSalesLegendItem {
  label: string;
  color: string;
}

export interface TotalSalesDatum {
  year: string;
  desktop: number;
  tablet: number;
  mobile: number;
}

export interface TotalSalesData {
  title: string;
  legend: TotalSalesLegendItem[];
  series: TotalSalesDatum[];
}

export const totalSalesData: TotalSalesData = {
  title: 'Vendas totais',
  legend: [
    { label: 'Desktops', color: '#5fbeaa' },
    { label: 'Tablets', color: '#5d9cec' },
    { label: 'Celulares', color: '#ebeff2' }
  ],
  series: [
    { year: '2009', desktop: 10, tablet: 20, mobile: 30 },
    { year: '2010', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2011', desktop: 50, tablet: 40, mobile: 30 },
    { year: '2012', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2013', desktop: 50, tablet: 40, mobile: 30 },
    { year: '2014', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2015', desktop: 90, tablet: 60, mobile: 30 }
  ]
};
