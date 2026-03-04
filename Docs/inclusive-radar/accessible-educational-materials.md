## 📚 Materiais Pedagógicos Acessíveis (AccessibleEducationalMaterial)

> Gestão de recursos didáticos adaptados, controlando desde a disponibilidade física e licenças digitais até os recursos de acessibilidade específicos e treinamentos para educadores.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Ciclo de Cadastro e Estrutura**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-MPA-01` | **Estrutura Obrigatória:** Nome, tipo (digital/físico), status, estado de conservação, inspeção e vínculo com ao menos uma deficiência. |
| `RF-MPA-02` | **Tipificação de Estoque:** Materiais físicos exigem quantidade $\ge 1$. Para digitais, a quantidade é opcional (licenças ilimitadas ou controladas). |
| `RF-MPA-03` | **Configuração de Empréstimo:** Permitir definir se o material é passível de empréstimo externo (`is_loanable`). |
| `RF-MPA-04` | **Recursos Extras:** Possibilidade de associar recursos de acessibilidade específicos ao material (ex: Braille, audiodescrição). |
| `RF-MPA-07` | **Patrimônio:** Registro opcional de código patrimonial (`asset_code`) para itens físicos. |
| `RF-MPA-15` | **Treinamentos:** Gestão de capacitações associadas. Na edição, os dados antigos são substituídos integralmente pelos novos. |

#### **2. Controle de Disponibilidade**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-MPA-05` | **Cálculo Automático:** A `quantity_available` deve ser recalculada dinamicamente subtraindo os empréstimos ativos da quantidade total. |
| `RF-MPA-06` | **Validação de Teto:** Impedir que a quantidade disponível exceda o estoque total cadastrado. |
| `RF-MPA-11` | **Trava de Redução:** Bloquear a redução do estoque total caso a nova quantidade seja insuficiente para cobrir os materiais que já estão emprestados. |

#### **3. Inspeção e Manutenção**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-MPA-08` | **Vistoria Inicial:** Obrigatoriedade de inspeção no cadastro (tipo "Inicial") com data atual ou retroativa. |
| `RF-MPA-09` | **Gestão de Mídia:** Upload de imagens (JPEG, PNG, JPG, WEBP) com limite de **2MB** por arquivo. |
| `RF-MPA-13` | **Log de Conservação:** Gerar nova inspeção automática se houver mudança no estado de conservação ou novas fotos/descrições na edição. |

#### **4. Regras de Segurança e Auditoria**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-MPA-12` | **Bloqueio de Status:** Impedir mudança de status (ex: "Arquivado") enquanto houver empréstimos pendentes. |
| `RF-MPA-14` | **Exclusão Restrita:** O material só pode ser removido do sistema se não possuir nenhum vínculo de empréstimo ativo. |
| `RF-MPA-16` | **AuditLog Detalhado:** Rastrear alterações em deficiências, recursos de acessibilidade e treinamentos, registrando "De:" e "Para:". |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-MPA-01` | **Integridade** | Uso obrigatório de **Database Transactions** em operações de escrita (CUD). |
| `RNF-MPA-02` | **Concorrência** | Aplicação de `lockForUpdate` em consultas de saldo para evitar *Race Conditions*. |
| `RNF-MPA-03` | **Arquitetura** | Armazenamento de imagens estruturado por pastas vinculadas ao ID da inspeção. |
| `RNF-MPA-04` | **UX/UI** | Feedback de erro claro, em português e focado na solução para o usuário. |
| `RNF-MPA-05` | **Padronização** | Normalização automática de campos booleanos e strings para garantir a consistência do banco. |

---
