# Convenção de testes do backend Gnai

Esta convenção segue o padrão de testes da branch `main`. Consulte-a sempre que criar, alterar ou remover testes, junto do `AGENTS.md` da raiz. Instruções explícitas do usuário prevalecem.

## Quando escrever testes

- Teste comportamento de negócio que pode quebrar: regras de domínio, invariantes, transições de estado, validações relevantes, efeitos persistidos, permissões e contratos expostos às telas.
- Não escreva testes triviais: getters, casts simples, propriedades atribuídas por `fill`, relações declarativas sem regra, mensagens literais copiadas do código ou estruturas que só repetem a implementação.
- Não duplique o mesmo comportamento em vários níveis. Se a Action de criar já prova a regra pelo banco, o teste unitário do model só cobre o que a Action não exercita.
- Um recurso unificado tem uma única suíte. Não mantenha suítes paralelas para variações do mesmo model.
- Teste quebrado por refatoração é atualizado ou removido junto da mudança; não deixe a suíte vermelha.

## Onde ficam

Os testes ficam no domínio dono do código, espelhando a estrutura de `app/Domains/<Dominio>/`:

| O que se testa | Caminho |
| --- | --- |
| Actions, Queries e fluxos com banco | `app/Domains/<Dominio>/Tests/Feature/<Recurso>/<Classe>Test.php` |
| Mapeamento e padrões dos Data | `app/Domains/<Dominio>/Tests/Unit/Application/Data/<Recurso>/<Data>Test.php` |
| Policies de aplicação | `app/Domains/<Dominio>/Tests/Unit/Application/Policies/<Recurso>/<Policy>Test.php` |
| Regras de domínio dos models | `app/Domains/<Dominio>/Tests/Unit/Domain/Models/<Model>Test.php` |
| Value objects | `app/Domains/<Dominio>/Tests/Unit/Domain/ValueObjects/<ValueObject>Test.php` |
| Código transversal de `app/Shared` e integrações gerais | `tests/Unit` e `tests/Feature` |

O domínio precisa estar listado na suíte `Domains` do `phpunit.xml` para os testes rodarem.

## Formato

- PHPUnit em classes `final`, com `declare(strict_types=1);` e namespace igual ao caminho.
- Estendem `Tests\TestCase`. Testes que tocam o banco usam `RefreshDatabase`. Testes puros podem estender `PHPUnit\Framework\TestCase`.
- Métodos `public function test_it_<comportamento_em_ingles>(): void`, em snake_case, descrevendo o comportamento e não o método testado.
- Arrange, act e assert separados por linhas em branco. Dados montados com factories e seus estados (`Item::factory()->physical()->loanable()->create()`).
- Actions são resolvidas pelo container e recebem o Data com argumentos nomeados: `app(CreateXAction::class)->execute(new CreateXData(...))`.
- Assertivas `self::assertSame`, `self::assertTrue` e similares, e `$this->assertDatabaseHas`/`assertDatabaseMissing` para efeitos persistidos.
- Exceções de domínio com `$this->expectException(InvalidX::class)` e, quando a mensagem é parte do contrato, `expectExceptionMessage`.
- Data testados com `XData::from([...snake_case...])`, conferindo o mapeamento e os padrões dos checkboxes; regras só quando há lógica condicional.
- Telas Inertia testadas pelo contrato (`assertInertia` com componente e props essenciais), sem repetir todos os campos.
- Sem constantes de classe para reaproveitar dados; prefira valores inline ou um método privado de montagem curto no próprio teste.

## Execução

- Suíte padrão: `make test` (Unit e Domains). Integração das páginas Inertia: `make test TEST=tests/Feature/InertiaPagesTest.php`.
- Para ver o resultado de cada teste sem depender do formatador do `artisan test`, use `docker compose --env-file .env.dev -f docker-compose.dev.yml exec app php vendor/bin/phpunit --testsuite Unit,Domains,Feature`.
- Em migrações comuns de telas continuam valendo os limites do `AGENTS.md`: não crie nem rode testes automaticamente sem pedido. Quando a tarefa envolver testes, siga esta convenção e entregue a suíte verde.
