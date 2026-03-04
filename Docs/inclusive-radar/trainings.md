## 📖 Treinamentos (Training)

> Repositório de conhecimento e capacitação técnica associado a materiais e tecnologias. Este módulo centraliza manuais, videoaulas (via URL) e documentos instrutivos para garantir o uso correto dos recursos pedagógicos e assistivos.

---

### 📋 Requisitos Funcionais (RF)

#### **1. Estrutura de Conteúdo**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TRE-01` | **Vínculo Polimórfico:** Permitir a associação de treinamentos a Materiais ou Tecnologias (`trainable`). |
| `RF-TRE-02` | **Atributos do Curso:** Registro obrigatório de Título; campos opcionais para Descrição, lista de URLs (validadas) e anexos de arquivos. |
| `RF-TRE-06` | **Sanitização de Links:** O sistema deve filtrar e remover automaticamente URLs vazias ou nulas antes da persistência. |

#### **2. Gestão de Arquivos e Mídia**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TRE-03` | **Restrições de Upload:** Suporte a arquivos PDF, DOC, DOCX, ZIP, JPG e PNG, com limite rigoroso de **10MB** por arquivo. |
| `RF-TRE-05` | **Exclusão com Purge:** Ao remover um treinamento, o sistema deve realizar a limpeza física de todos os anexos e do diretório no storage. |

#### **3. Regras de Manutenção**

| Código | Descrição Detalhada |
| --- | --- |
| `RF-TRE-04` | **Imutabilidade de Vínculo:** Permitir a edição de conteúdos (título, arquivos, links), mas impedir a troca do item pai (`trainable`) após o cadastro inicial. |

---

### ⚙️ Requisitos Não Funcionais (RNF)

| Código | Categoria | Descrição |
| --- | --- | --- |
| `RNF-TRE-01` | **Integridade** | Operações de escrita e gestão de arquivos devem ser executadas em **Database Transactions**. |
| `RNF-TRE-02` | **Arquitetura** | Armazenamento físico de arquivos organizado em subpastas exclusivas identificadas pelo **ID do treinamento**. |
| `RNF-TRE-03` | **FileSystem** | A rotina de exclusão deve garantir a remoção recursiva de arquivos e diretórios para evitar o acúmulo de dados órfãos. |

---
