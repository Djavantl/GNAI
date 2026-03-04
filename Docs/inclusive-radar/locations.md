## 📍 Locais / Pontos de Referência (Location)

> Gerenciamento dos espaços físicos dentro ou vinculados a uma instituição (ex: blocos, laboratórios, auditórios). Estes pontos servem como ancoragem geográfica para a identificação de barreiras e alocação de recursos.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Estrutura Geográfica**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-LOC-01` | **Cadastro e Vínculo:** Registro obrigatório de Nome, Latitude e Longitude, sempre vinculado a uma Instituição pai. |
| `RF-LOC-01` | **Atributos Complementares:** Possibilidade de definir o Tipo (sala, pátio, etc.), Descrição detalhada e integração via `google_place_id`. |
| `RF-LOC-02` | **Edição de Dados:** Permitir a atualização das informações cadastrais e das coordenadas geográficas do local. |

#### **2. Regras de Integridade e Status**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-LOC-03` | **Bloqueio de Inativação:** Impedir que um local seja marcado como inativo (`is_active = false`) se houver barreiras abertas ou não resolvidas associadas a ele. |
| `RF-LOC-04` | **Exclusão Restrita:** A remoção definitiva do local só é permitida caso não existam pendências de barreiras vinculadas, preservando o histórico de acessibilidade. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-LOC-01` | **Integridade** | Operações de escrita e atualizações de status devem ser executadas dentro de **Database Transactions**. |
| `RNF-LOC-02` | **Geoprocessamento** | Validação rigorosa de coordenadas: Latitude ($[-90, 90]$) e Longitude ($[-180, 180]$). |
| `RNF-LOC-03` | **Localização** | Interface e mensagens de erro totalmente em **PT-BR**. |
| `RNF-LOC-04` | **Sanitização** | Garantir a normalização do campo `is_active` para o tipo booleano no ciclo de vida da Request. |

---
