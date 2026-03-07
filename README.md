
---

# 🎓 GNAI - Gestor do Núcleo de Acessibilidade e Inclusão

O **GNAI** é uma plataforma integrada de gestão voltada para a promoção da acessibilidade e suporte à inclusão em ambientes educacionais. O sistema foi desenvolvido como **Trabalho de Conclusão de Curso (TCC)** para o curso de **Análise e Desenvolvimento de Sistemas (ADS)**.

Embora o desenvolvimento das funcionalidades tenha sido realizado de forma **individual**, os módulos compartilham o mesmo repositório estratégico para viabilizar e facilitar a integração futura entre as duas frentes de trabalho.

---

## 🏛️ Contextualização do Projeto

O sistema nasce da necessidade de centralizar processos que hoje são muitas vezes dispersos ou manuais nas instituições de ensino. Ele é dividido em dois grandes pilares complementares:

### 🎯 Atendimento Educacional Especializado (AEE)

*Responsável: Djavan Teixeira Lopes* - Focado na jornada pedagógica do estudante com deficiência, gerenciando planos de atendimento, avaliações de desenvolvimento e o acompanhamento direto entre o professor e o aluno.

### 📡 Radar Inclusivo

*Responsável: Marley Teixeira Meira* - Focado na infraestrutura e logística de acessibilidade. Gerencia desde a identificação de barreiras físicas e atitudinais na instituição até o controle de estoque, empréstimos e manutenção de tecnologias assistivas e materiais pedagógicos adaptados.

---

## 🛠️ Por que um repositório único?

A decisão de utilizar um repositório compartilhado visa:

1. **Consistência de Dados:** Garantir que o estudante cadastrado no módulo AEE seja o mesmo beneficiário de um empréstimo no Radar Inclusivo.
2. **Padronização Tecnológica:** Ambos os módulos utilizam a mesma stack (Laravel 12 + Docker), garantindo uma manutenção simplificada.
3. **Interoperabilidade:** Facilita a criação de relatórios gerenciais que cruzem dados de desempenho escolar com a disponibilidade de recursos de acessibilidade.

---

### 🛠️ Stack Tecnológica & Infraestrutura

| Componente | Tecnologia | Papel no Projeto |
| --- | --- | --- |
| **Linguagem** | `PHP 8.4` | Backend robusto com tipagem forte. |
| **Framework** | `Laravel 12` | Estrutura de ponta para aplicações web. |
| **Frontend** | `Node 20 / Vite` | Compilação ultra-rápida de assets. |
| **Banco de Dados** | `MySQL 8.0` | Armazenamento relacional seguro. |
| **Container** | `Docker / Compose` | Padronização de ambiente (Dev/Prod). |
| **Servidor Web** | `Nginx Alpine` | Proxy reverso leve e performático. |

---

# Documentação de Requisitos: AEE & Radar Inclusivo

## 1. Módulo: Atendimento Educacional Especializado (AEE)

**Responsável:** [Djavan Teixeira Lopes]

| Tipo | Descrição do Requisito |
| --- | --- |
| **RF** | *Aguardando definições de requisitos do responsável pelo módulo.* |
| **RNF** | *Aguardando definições de requisitos do responsável pelo módulo.* |

---

## 2. Módulo: Radar Inclusivo

**Responsável:** [Marley Teixeira Meira]

---

## 📚 Documentação Corrigida

| Contexto                                                                                       | O que é / Para que serve                                                                                                                                               |
| ---------------------------------------------------------------------------------------------- |------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Recursos de Acessibilidade](Docs/inclusive-radar/accessibility-features.md)                   | Categorias que definem como um material ou tecnologia assistiva pode ser acessível a pessoas com deficiência. Cada recurso descreve um tipo de adaptação ou suporte.   |
| [Materiais e Tecnologias Assistivas](Docs/inclusive-radar/accessible-educational-materials.md) | Materiais físicos ou digitais disponibilizados para apoiar o aprendizado, como livros adaptados, softwares acessíveis ou equipamentos específicos.                     |
| [Empréstimos](Docs/inclusive-radar/loans.md)                                                   | Controle de quando um material ou tecnologia assistiva é emprestado a um aluno ou profissional, com registro de datas e status.                                        |
| [Treinamentos](Docs/inclusive-radar/trainings.md)                                              | Conteúdos e orientações para ensinar a utilização correta de materiais ou tecnologias assistivas.                                                                      |
| [Lista de Espera](Docs/inclusive-radar/waitlists.md)                                           | Fila organizada para itens indisponíveis. O sistema notifica o próximo da fila quando o material ou equipamento fica disponível.                                       |
| [Inspeções](Docs/inclusive-radar/inspections.md)                                               | Registro das verificações periódicas nos materiais e equipamentos para garantir estado de conservação adequado.                                                        |
| [Barreiras](Docs/inclusive-radar/barriers.md)                                                  | Registro de obstáculos na instituição que prejudicam a acessibilidade, como falta de rampas ou sinalização inadequada.                                                 |
| [Instituições](Docs/inclusive-radar/institutions.md)                                           | Cadastro das instituições atendidas pelo sistema, incluindo informações básicas e coordenadas geográficas.                                                             |
| [Locais / Pontos de Referência](Docs/inclusive-radar/locations.md)                             | Cadastro dos espaços físicos dentro da instituição, permitindo associar materiais, barreiras e acessibilidade.                                                         |
| [Geocodificação](Docs/inclusive-radar/openstreetmap.md)                                        | Serviço que transforma um endereço em coordenadas geográficas, integrando o mapa da instituição.                                                                       |
| [Requisitos Não Funcionais Gerais](Docs/inclusive-radar/general.md)                            | Regras gerais para segurança, organização, controle e bom funcionamento do sistema como um todo.                                                                       |

---
