## 🏫 Instituições (Institution)

> Entidade central do sistema que define o contexto geográfico e administrativo. O sistema opera sob um modelo de instância única, onde a instituição serve como o nó principal para locais, materiais e barreiras.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Configuração e Localização**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-INST-01` | **Atributos Obrigatórios:** Nome, Cidade, Estado e Coordenadas Geográficas (Latitude/Longitude). |
| `RF-INST-01` | **Atributos Opcionais:** Nome curto (sigla), distrito, endereço completo e nível de zoom padrão para mapas. |
| `RF-INST-03` | **Edição:** Permitir a atualização de todos os dados cadastrais e de geolocalização da entidade. |

#### **2. Regras de Existência (Singleton)**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-INST-02` | **Instância Única:** O sistema deve garantir que exista apenas **uma** instituição cadastrada. Tentativas de criar uma segunda devem ser bloqueadas. |

#### **3. Ciclo de Vida e Dependências**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-INST-04` | **Trava de Inativação:** Impedir que a instituição seja desativada (`is_active = false`) caso possua barreiras ativas ou não resolvidas vinculadas. |
| `RF-INST-05` | **Desativação em Cascata:** Ao desativar a instituição, todos os **locais** (salas, blocos, anexos) vinculados a ela devem ser inativados automaticamente. |
| `RF-INST-06` | **Exclusão Segura:** A remoção definitiva do registro só é permitida se não houver nenhuma barreira pendente de finalização. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-INST-01` | **Integridade** | Operações de escrita, especialmente as desativações em cascata, devem ser **Transacionais**. |
| `RNF-INST-02` | **Geolocalização** | Validar coordenadas: Latitude ($[-90, 90]$) e Longitude ($[-180, 180]$). |
| `RNF-INST-03` | **UX/UI** | Todas as notificações de erro e sucesso devem ser exibidas em **PT-BR**. |
| `RNF-INST-04` | **Sanitização** | Normalizar o campo `is_active` para o tipo booleano no ciclo da Request. |
| `RNF-INST-05` | **Regra de Negócio** | A lógica de **Singleton** deve ser validada na camada de serviço ou via regra customizada na Request de criação. |

---
