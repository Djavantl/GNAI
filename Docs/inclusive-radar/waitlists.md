## ⏳ Lista de Espera (Waitlist)

> Gestão de demandas reprimidas para materiais e tecnologias. Este módulo organiza a fila de prioridade quando um recurso está indisponível, notificando beneficiários e automatizando a baixa da solicitação após o atendimento.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Regras de Entrada e Elegibilidade**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-WAI-01` | **Gatilho de Entrada:** Permitir a inclusão na fila apenas quando o item estiver com estoque zerado ou status que impossibilite o empréstimo imediato. |
| `RF-WAI-02` | **Unicidade de Fila:** Impedir que um beneficiário possua mais de uma solicitação ativa (`waiting` ou `notified`) para o mesmo recurso. |
| `RF-WAI-03` | **Conflito de Posse:** Proibir a entrada na lista de espera caso o beneficiário já possua um empréstimo ativo do item em questão. |
| `RF-WAI-04` | **Responsabilidade:** Registrar automaticamente o ID do usuário autenticado como o operador que inseriu o beneficiário na fila. |

#### **2. Fluxo de Estados e Ciclo de Vida**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-WAI-05` | **Máquina de Estados:** O status deve seguir rigorosamente o fluxo: `waiting` (Aguardando) $\rightarrow$ `notified` (Notificado) $\rightarrow$ `fulfilled` (Atendido) ou `cancelled` (Cancelado). |
| `RF-WAI-06` | **Edição Restrita:** Solicitações finalizadas (`fulfilled`/`cancelled`) só podem ter o campo de observação alterado; o status torna-se imutável. |
| `RF-WAI-07` | **Cancelamento:** O cancelamento voluntário é permitido apenas enquanto a solicitação estiver no estado inicial (`waiting`). |
| `RF-WAI-08` | **Exclusão:** Proibir a remoção física de solicitações que já foram atendidas, preservando o histórico de demanda. |

#### **3. Inteligência de Fila e Atendimento**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-WAI-09` | **Prioridade (FIFO):** Fornecer mecanismo para selecionar a solicitação mais antiga (Primeiro a Entrar, Primeiro a Sair) e transicioná-la para o status `notified`. |
| `RF-WAI-10` | **Baixa Automática:** Ao efetivar um novo empréstimo, o sistema deve verificar se o beneficiário está na fila para aquele item e marcá-lo como `fulfilled` automaticamente. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-WAI-01` | **Integridade** | Operações de transição de status e criação de registros devem ser executadas em **Database Transactions**. |
| `RNF-WAI-02` | **Concorrência** | Uso obrigatório de `lockForUpdate` na consulta do item para evitar que dois beneficiários ocupem a mesma posição na fila simultaneamente. |
| `RNF-WAI-03` | **Auditoria** | Rastreabilidade garantida pelo vínculo do `user_id` em todas as interações com a lista de espera. |

---
