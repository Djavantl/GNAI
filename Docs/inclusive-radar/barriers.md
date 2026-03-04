## 🚧 Barreiras (Barrier)

> Registro e monitoramento de obstáculos que limitam a acessibilidade. Este módulo centraliza o diagnóstico de problemas, a prioridade de resolução e o rastreio de inspeções, permitindo identificar o impacto real sobre estudantes e profissionais.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Ciclo de Registro e Identificação**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-BAR-01` | **Estrutura de Cadastro:** Exigir nome, instituição, categoria, prioridade (low a critical), data de identificação, inspeção inicial e vínculo com ao menos uma deficiência. |
| `RF-BAR-05` | **Multideficiência:** Permitir que uma única barreira seja associada a múltiplas deficiências simultaneamente. |
| `RF-BAR-07` | **Autoria:** Registrar automaticamente o ID do usuário autenticado como o responsável pela abertura do chamado. |
| `RF-BAR-11` | **Exclusão:** Permitir a remoção do registro de barreira do sistema. |

#### **2. Identificação do Afetado (Lógica Condicional)**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-BAR-02` | **Modo Anônimo:** Campo `is_anonymous` que, se ativo, limpa e protege os dados de identificação da pessoa afetada. |
| `RF-BAR-03` | **Relato Geral:** Campo `not_applicable` para casos que não se aplicam a um usuário específico do sistema, exigindo Nome e Cargo textuais. |
| `RF-BAR-04` | **Vínculo Nominal:** Caso não seja anônimo ou geral, é obrigatório vincular ao menos um `student_id` ou `professional_id` cadastrado. |

#### **3. Inspeção, Mídia e Edição**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-BAR-06` | **Gestão de Imagens:** Suporte a fotos de evidência (JPEG, PNG, JPG, WEBP) com limite de **5MB** por arquivo. |
| `RF-BAR-08` | **Sincronização:** Na edição, o sistema deve atualizar os dados básicos e sincronizar as relações de deficiências afetadas. |
| `RF-BAR-10` | **Histórico de Inspeção:** Gerar automaticamente um novo registro de inspeção sempre que houver mudança de status ou novas evidências (fotos/descrição). |

#### **4. Fluxo de Status e Resolução**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-BAR-09` | **Data de Resolução:** Ao definir o status como "Resolvido" ou "Não Aplicável", o sistema deve preencher `resolved_at` automaticamente com o *timestamp* atual. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-BAR-01` | **Integridade** | Todas as operações de persistência e vínculos devem ser executadas em **Transactions**. |
| `RNF-BAR-02` | **Validação** | Regras complexas de dependência (Anônimo vs. Geral vs. ID) devem ser validadas no `withValidator`. |
| `RNF-BAR-03` | **Sanitização** | Campos booleanos devem ser normalizados no `prepareForValidation`. |
| `RNF-BAR-04` | **Armazenamento** | Imagens devem ser geridas pelo `InspectionService`, organizadas por diretórios de inspeção. |
| `RNF-BAR-05` | **Consistência** | A data da inspeção é obrigatória e nunca pode ser maior que a data atual. |
| `RNF-BAR-06` | **UX/UI** | Mensagens de erro e validação devem ser apresentadas em **PT-BR**. |
| `RNF-BAR-07` | **Padronização** | Uso estrito de **Enums** para Prioridade, Status e Tipos de Inspeção. |

---
