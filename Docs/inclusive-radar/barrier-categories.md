## 🚧 Categorias de Barreira (BarrierCategory)

> Classificação de obstáculos (arquitetônicos, comunicacionais, atitudinais, etc.) que impedem a plena participação do usuário, servindo como base para o diagnóstico de acessibilidade.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Ciclo de Cadastro e Identificação**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-CAT-01` | **Atributos Base:** Cadastro composto por **Nome** (obrigatório) e **Descrição** (opcional para detalhamento do tipo de barreira). |
| `RF-CAT-02` | **Unicidade Inteligente:** O nome deve ser único, mas a validação deve ignorar registros que sofreram *Soft Delete*. |
| `RF-CAT-03` | **Gestão de Status:** Controle de ativação via `is_active`, permitindo suspender o uso de uma categoria sem apagar seus dados. |

#### **2. Manutenção e Restrições**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-CAT-04` | **Edição de Dados:** Permitir a atualização do nome e descrição conforme a evolução das normas de acessibilidade. |
| `RF-CAT-05` | **Exclusão Segura:** Impedir a remoção de categorias que possuam barreiras vinculadas ainda não finalizadas ou ativas. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-CAT-01` | **Integridade** | Operações de escrita (CUD) devem ser encapsuladas em **Database Transactions**. |
| `RNF-CAT-02` | **Persistência** | A lógica de unicidade do banco de dados deve considerar a coluna `deleted_at`. |
| `RNF-CAT-03` | **Localização** | Feedback de validação e mensagens do sistema obrigatoriamente em **PT-BR**. |
| `RNF-CAT-04` | **Sanitização** | Normalizar o campo `is_active` para booleano puro no ciclo de vida da Request (`prepareForValidation`). |

---
