# Gráficos

As dez rotas da seção Charts foram implementadas em React. Os mesmos motores, dados demonstrativos, fontes e estilos dos plugins são usados dentro de componentes React com ciclo de montagem e desmontagem; Bootstrap não foi adicionado à aplicação React.

## Telas e componentes

| Rota | Cards | Gráficos | Componente |
| --- | ---: | ---: | --- |
| `chart-flot.html` | 6 | 6 | `FlotChart` |
| `chart-morris.html` | 6 | 6 | `MorrisChart` |
| `chart-chartjs.html` | 7 | 7 | `ChartJsChart` |
| `chart-peity.html` | 4 | 23 | `PeityChart` |
| `chart-chartist.html` | 22 | 22 | `ChartistChart` |
| `chart-c3.html` | 8 | 8 | `C3Chart` |
| `chart-nvd3.html` | 5 | 6 | `Nvd3Chart` |
| `chart-sparkline.html` | 6 | 6 | `SparklineChart` |
| `chart-radial.html` | 5 | 24 | `RadialChart` |
| `chart-other.html` | 3 | 19 | `KnobChart`, `EasyPieChart`, `CirclifulChart` |

Cada componente tem pasta própria. `ChartsPage` orquestra as seções e os arquivos `.data.ts` mantêm as configurações dos exemplos. `hooks/useChart` centraliza carregamento de scripts, observação de largura e descarte dos motores. As APIs dinâmicas dos plugins antigos ficam restritas às integrações; props e configurações públicas são tipadas.

Dashboard 1, Dashboard 2, Dashboard 3, Widgets e os indicadores de pedidos passaram a consumir os componentes compartilhados. Os desenhos SVG manuais dos indicadores do Dashboard 2 e do mostrador do Dashboard 1 foram substituídos pelos respectivos motores Peity e Knob. Estilos do tooltip Morris foram centralizados.
