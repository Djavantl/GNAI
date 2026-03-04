## 🤝 Empréstimos (Loan)

> Gestão do ciclo de vida de uso dos materiais e tecnologias, desde a reserva e saída até a devolução e controle de integridade do item emprestado.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Registro e Saída**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-EMP-01` | **Exclusividade:** Permitir o empréstimo de um item (Material ou Tecnologia) para um beneficiário (Estudante ou Profissional) cadastrado. |
| `RF-EMP-02` | **Polimorfismo:** Restringir os itens emprestáveis apenas aos modelos permitidos via *Morph Map* (Materiais/Tecnologias). |
| `RF-EMP-03` | **Prazos:** Data de empréstimo (atual por padrão) e data prevista de devolução (obrigatória e obrigatoriamente futura). |
| `RF-EMP-04` | **Responsabilidade:** Vincular automaticamente o ID do usuário autenticado como o operador que realizou o empréstimo. |
| `RF-EMP-13` | **Baixa em Espera:** Se houver uma solicitação na lista de espera para aquele item e beneficiário, marcá-la automaticamente como **Atendida**. |

#### **2. Regras de Disponibilidade e Estoque**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-EMP-05` | **Check de Pré-requisitos:** Validar se a quantidade disponível é $> 0$, se o status/conservação do item permite a saída e se o beneficiário já não possui o mesmo item ativo. |
| `RF-EMP-06` | **Baixa Automática:** Decrementar o saldo disponível do item imediatamente após a efetivação do empréstimo. |
| `RF-EMP-07` | **Gatilho de Status:** Alterar o status do item para **"EM USO"** caso a quantidade disponível atinja zero após a operação. |

#### **3. Devolução e Manutenção**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-EMP-08` | **Triagem de Retorno:** Registrar data real de devolução e classificar o status do empréstimo como: "Devolvido" (no prazo), "Atrasado" ou "Danificado". |
| `RF-EMP-09` | **Reposicionamento:** Incrementar a quantidade disponível e atualizar o status do item (se devolvido íntegro → "Disponível"; se avariado → "Danificado"). |
| `RF-EMP-12` | **Monitoramento:** Fornecer listagem rápida de todos os empréstimos que excederam o prazo de devolução previsto. |

#### **4. Edição e Exclusão**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-EMP-10` | **Imutabilidade Parcial:** Por segurança, permitir apenas a edição do campo de observações em empréstimos já consolidados. |
| `RF-EMP-11` | **Reversão:** Se um empréstimo ativo for excluído, o sistema deve restaurar automaticamente a quantidade disponível do item. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-EMP-01` | **Integridade** | Toda a lógica de atualização de saldo e criação/baixa de empréstimo deve rodar em **Transactions**. |
| `RNF-EMP-02` | **Concorrência** | Uso de `lockForUpdate` na consulta do item para garantir que a quantidade disponível não seja consumida por processos simultâneos. |
| `RNF-EMP-03` | **Auditoria** | Registro obrigatório do `user_id` em cada transação para fins de rastreabilidade. |
| `RNF-EMP-04` | **Segurança** | Validações de domínio (prazos e estoque) devem ser disparadas antes de qualquer escrita no banco de dados. |
| `RNF-EMP-05` | **UX/UI** | Feedback de erro claro, em português e focado no motivo do bloqueio (ex: "Item sem estoque"). |

---
