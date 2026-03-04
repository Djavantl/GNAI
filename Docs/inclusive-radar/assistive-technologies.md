## 🦾 Tecnologias Assistivas (AssistiveTechnology)

> Gestão de recursos físicos e digitais voltados à acessibilidade, garantindo controle de estoque, integridade de equipamentos e conformidade com as necessidades dos usuários.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Ciclo de Cadastro e Estrutura**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TA-01` | **Estrutura Obrigatória:** Nome, tipo (digital/físico), status, estado de conservação, inspeção e vínculo com ao menos uma deficiência. |
| `RF-TA-02` | **Tipificação:** Diferenciar itens físicos de licenças digitais, herdando as regras de estoque de materiais comuns. |
| `RF-TA-05` | **Patrimônio:** Permitir o registro opcional de um código patrimonial único (UUID/Tag). |
| `RF-TA-12` | **Treinamentos:** Gerenciar treinamentos de uso. Em edições, a lista de treinamentos deve ser substituída integralmente pela nova seleção. |

#### **2. Controle de Estoque e Integridade**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TA-03` | **Disponibilidade Automática:** O saldo em estoque deve ser atualizado em tempo real com base nos empréstimos ativos. |
| `RF-TA-04` | **Validação de Teto:** A quantidade disponível jamais pode ser superior à quantidade total cadastrada. |
| `RF-TA-08` | **Recálculo em Edição:** Ao reduzir o estoque total, o sistema deve impedir a ação se a nova quantidade for menor que o número de itens emprestados no momento. |

#### **3. Inspeção e Mídia**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TA-06` | **Inspeção de Entrada:** Exigir inspeção inicial com data retroativa ou atual (proibir datas futuras). |
| `RF-TA-07` | **Uploads:** Suporte a fotos do item/inspeção seguindo os padrões globais de segurança e formato do sistema. |
| `RF-TA-10` | **Histórico Automático:** Registrar logs de inspeção sempre que houver mudanças críticas no estado do item. |

#### **4. Regras de Bloqueio e Segurança**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TA-09` | **Trava de Status:** Impedir a alteração de status (ex: "Em Manutenção") se o item estiver com um usuário. |
| `RF-TA-11` | **Exclusão Segura:** Só permitir a deleção do registro se não houver histórico de empréstimos pendentes. |
| `RF-TA-13` | **AuditLog:** Rastrear quem alterou relações de deficiências e treinamentos associados. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-TA-01` | **Integridade** | Todas as persistências devem rodar sob **Transactions** (Tudo ou Nada). |
| `RNF-TA-02` | **Concorrência** | Implementar `lockForUpdate` para evitar erros de saldo em acessos simultâneos. |
| `RNF-TA-03` | **Arquitetura** | Arquivos de mídia devem ser organizados em subdiretórios por ID do item: `/assets/ta/{id}/...`. |
| `RNF-TA-04` | **UX/UI** | Erros e validações devem ser retornados em **PT-BR** amigável, sem códigos técnicos puros. |

---
