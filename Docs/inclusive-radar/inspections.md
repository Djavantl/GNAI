## 🔍 Inspeções (Inspection)

> Registro sistemático do estado de conservação e integridade de materiais, tecnologias e barreiras. Este módulo garante a rastreabilidade das condições físicas dos recursos ao longo do tempo.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Ciclo de Vida e Registro**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-INS-01` | **Estrutura de Dados:** Registro obrigatório de data, tipo (Inicial/Periódica/Saída), estado de conservação, descrição e o usuário responsável. |
| `RF-INS-03` | **Automação de Cadastro:** O sistema deve gerar obrigatoriamente uma "Inspeção Inicial" no momento da criação de qualquer item (Material ou Tecnologia). |
| `RF-INS-04` | **Gatilhamento Inteligente:** Na edição de um item, criar uma nova inspeção **apenas se** houver alteração no estado de conservação ou se novas evidências (fotos/texto) forem incluídas. |

#### **2. Evidências e Limpeza**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-INS-02` | **Anexos Fotográficos:** Suporte a imagens seguindo os padrões globais do sistema (Formatos: JPEG, PNG, JPG, WEBP; Tamanho: Conforme regra do material pai). |
| `RF-INS-05` | **Exclusão com Purge:** Ao remover uma inspeção, o sistema deve realizar a limpeza física (deletar arquivos e diretórios) no servidor de arquivos. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-INS-01` | **Integridade** | Todas as persistências de dados e logs de inspeção devem rodar sob **Transactions**. |
| `RNF-INS-02` | **Arquitetura** | Armazenamento físico de imagens organizado obrigatoriamente em subpastas nomeadas com o **ID da inspeção**. |
| `RNF-INS-03` | **Manutenibilidade** | A rotina de exclusão deve garantir a remoção recursiva do diretório para evitar "arquivos órfãos" no storage. |

---
