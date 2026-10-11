export type RecentOrdersSparklineType = 'bar' | 'line' | 'pie';

export interface RecentOrdersSparkline {
  type: RecentOrdersSparklineType;
  values: string;
  width: number;
  height: number;
  fill?: string;
  stroke?: string;
  colors?: string[];
}

export interface RecentOrdersItem {
  id: string;
  productName: string;
  productImage: string;
  trendText: string;
  trendTone: 'positive' | 'negative';
  sparkline: RecentOrdersSparkline;
}

export interface RecentOrdersData {
  title: string;
  actionLabel: string;
  description: string;
  columns: {
    product: string;
    change: string;
    manage: string;
  };
  items: RecentOrdersItem[];
}

export const recentOrdersData: RecentOrdersData = {
  title: 'Pedidos recentes',
  actionLabel: 'Ver todos',
  description: 'Use as classes de botão em um elemento.',
  columns: {
    product: 'Nome do item',
    change: 'Alta-Baixa',
    manage: 'Gerenciar'
  },
  items: [
    {
      id: 'iphone',
      productName: 'Apple iPhone',
      productImage: '/assets/react/images/products/iphone.jpg',
      trendText: '+$230',
      trendTone: 'positive',
      sparkline: {
        type: 'bar',
        values: '5,3,9,6,5,9,7,3,5,2',
        width: 80,
        height: 30,
        colors: ['#5fbeaa', '#5fbeaa']
      }
    },
    {
      id: 'samsung',
      productName: 'Samsung Phone',
      productImage: '/assets/react/images/products/samsung.jpg',
      trendText: '-$154',
      trendTone: 'negative',
      sparkline: {
        type: 'line',
        values: '0,-3,-6,-4,-5,-4,-7,-3,-5,-2',
        width: 80,
        height: 30,
        fill: '#5fbeaa',
        stroke: '#5fbeaa'
      }
    },
    {
      id: 'imac',
      productName: 'Apple iPhone',
      productImage: '/assets/react/images/products/imac.jpg',
      trendText: '+$1850',
      trendTone: 'positive',
      sparkline: {
        type: 'line',
        values: '5,3,9,6,5,9,7,3,5,2',
        width: 80,
        height: 30,
        fill: '#fff',
        stroke: '#5fbeaa'
      }
    },
    {
      id: 'macbook',
      productName: 'Apple iPhone',
      productImage: '/assets/react/images/products/macbook.jpg',
      trendText: '-$560',
      trendTone: 'negative',
      sparkline: {
        type: 'pie',
        values: '1/5',
        width: 30,
        height: 30,
        colors: ['#5fbeaa', '#ebeff2']
      }
    },
    {
      id: 'lumia',
      productName: 'Lumia iPhone',
      productImage: '/assets/react/images/products/lumia.jpg',
      trendText: '+$230',
      trendTone: 'positive',
      sparkline: {
        type: 'bar',
        values: '5,3,9,6,5,9,7,3,5,2',
        width: 80,
        height: 30,
        colors: ['#5fbeaa', '#ebeff2']
      }
    }
  ]
};
