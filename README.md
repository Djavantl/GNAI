
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

## 📚 Documentação

| Contexto                                                                                       | O que é / Para que serve                                                                                                                                                  |
|------------------------------------------------------------------------------------------------| ------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [Recursos de Acessibilidade](Docs/inclusive-radar/accessibility-features.md)                   | São ferramentas ou adaptações que ajudam a tornar materiais e ambientes mais acessíveis para pessoas com deficiência, como leitores de tela, legendas ou recursos táteis. |
| [Materiais e Tecnologias Assistivas](Docs/inclusive-radar/accessible-educational-materials.md) | São os materiais físicos ou digitais disponibilizados para apoiar o aprendizado, como livros adaptados, softwares acessíveis ou equipamentos específicos.                 |
| [Empréstimos](Docs/inclusive-radar/loans.md)                                                   | Área responsável por controlar quando um material ou equipamento é emprestado para um aluno ou profissional, registrando datas e devoluções.                              |
| [Treinamentos](Docs/inclusive-radar/trainings.md)                                              | Espaço onde são registrados conteúdos e orientações para ensinar como utilizar corretamente um material ou tecnologia assistiva.                                          |
| [Lista de Espera](Docs/inclusive-radar/waitlists.md)                                           | Funciona como uma fila organizada para quando um material está indisponível. A pessoa entra na fila e é avisada quando o item estiver disponível.                         |
| [Inspeções](Docs/inclusive-radar/inspections.md)                                               | Registro das verificações feitas nos materiais e equipamentos para garantir que estejam em bom estado de uso.                                                             |
| [Barreiras](Docs/inclusive-radar/barriers.md)                                                  | Registro de dificuldades ou obstáculos encontrados dentro da instituição que podem prejudicar a acessibilidade, como falta de rampas ou sinalização adequada.             |
| [Instituições e Locais](Docs/inclusive-radar/locations.md)                                     | Informações sobre a instituição e seus diferentes espaços físicos, permitindo identificar onde estão os materiais ou onde existe alguma barreira.                         |
| [Geocodificação](Docs/inclusive-radar/openstreetmap.md)                                        | Sistema que transforma um endereço em localização no mapa, ajudando a identificar coordenadas geográficas automaticamente.                                                |
| [Requisitos Não Funcionais Gerais](Docs/inclusive-radar/general.md)                            | Conjunto de regras que garantem segurança, organização, controle e bom funcionamento do sistema como um todo.                                                             |

---
