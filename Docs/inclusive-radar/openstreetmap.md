## 🗺️ Geocodificação (OpenStreetMap / Nominatim)

> Serviço de inteligência geográfica responsável por converter endereços textuais em coordenadas precisas (Latitude e Longitude), permitindo o mapeamento automático de Instituições, Locais e Barreiras.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Conversão e Busca**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-GEO-01` | **Integração Externa:** Prover um serviço de busca que consulte a API **Nominatim (OSM)** para obter coordenadas a partir de um endereço formatado. |
| `RF-GEO-02` | **Filtro de Relevância:** O sistema deve processar e retornar apenas o **primeiro resultado** (o de maior "importance") retornado pela API. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-GEO-01` | **Identificação** | O **User-Agent** da requisição HTTP deve ser obrigatoriamente configurável via `config/services.osm.user_agent` para cumprir as políticas de uso do Nominatim. |
| `RNF-GEO-02` | **Flexibilidade** | A **URL base** da API deve ser definida via arquivo de configuração (permitindo a troca para instâncias próprias ou pagas do serviço). |
| `RNF-GEO-03` | **Resiliência** | O serviço deve tratar respostas vazias (404 ou lista vazia) e timeouts, retornando `null` de forma segura para evitar quebras no fluxo de cadastro. |

---
