## 🏗️ Requisitos Não Funcionais Gerais (Core Infrastructure)

> Diretrizes transversais que definem a qualidade, segurança e sustentabilidade do sistema. Estes requisitos devem ser aplicados em todos os módulos para garantir uma arquitetura resiliente e padronizada.

---

### 📋 Arquitetura e Performance

| Código | Descrição Detalhada |
| --- | --- |
| `RNF-GER-08` | **Clean Code:** Centralizar regras de negócio em **Services** específicos, promovendo a reutilização de código e facilitando a manutenção. |
| `RNF-GER-09` | **Alta Performance:** Uso obrigatório de **Eager Loading** (carregamento adiantado) para evitar o problema de consultas $N+1$ e otimização via índices no banco. |
| `RNF-GER-10` | **Atomicidade:** Toda operação que envolva múltiplas tabelas ou registros deve ser encapsulada em uma **Database Transaction**. |

#### **Estrutura de Armazenamento**

| Código | Descrição Detalhada |
| --- | --- |
| `RNF-GER-06` | **File System:** Organização lógica de uploads em diretórios estruturados por modelo e ID (ex: `/storage/app/public/{model}/{id}/...`). |

---

### 🔐 Segurança e Auditoria

| Código | Descrição Detalhada |
| --- | --- |
| `RNF-GER-01` | **Autenticação:** O sistema deve obter o `user_id` diretamente da sessão autenticada para registros de autoria, impedindo a manipulação via Request. |
| `RNF-GER-02` | **Rastreabilidade (AuditLog):** Registrar quem, quando e de onde (IP/User Agent) alterou dados críticos, armazenando o estado anterior e o atual. |
| `RNF-GER-04` | **Race Conditions:** Utilizar `lockForUpdate` em operações de concorrência crítica, como reservas, empréstimos e filas de espera. |

---

### 🛡️ Integridade e Validação

| Código | Descrição Detalhada |
| --- | --- |
| `RNF-GER-03` | **Check de Existência:** Validar a integridade referencial de chaves estrangeiras (`Foreign Keys`) em todas as camadas de Request. |
| `RNF-GER-07` | **Validation Layer:** Todas as Requests devem validar tipos de dados, tamanhos e obrigatoriedade, com mensagens personalizadas em **PT-BR**. |
| `RNF-GER-05` | **Friendly Errors:** Exceções técnicas do sistema devem ser capturadas e convertidas em mensagens legíveis e amigáveis para o usuário final. |

---
