# Auditoria

O histórico de alterações registra toda criação, alteração, exclusão e restauração dos registros de negócio do Gnai. A consulta fica no item **Auditoria** do grupo **Outros** da sidebar (`/audit`), protegida pela permissão `audit.index`. Administradores têm acesso por padrão; para outros cargos, conceda a permissão depois de sincronizar o catálogo com `php artisan permissions:sync`.

O desenho segue a auditoria do projeto controle-bar, adaptado ao Gnai: sem multi-tenant, com nomes em inglês e com o "entrar como" no lugar da inspeção.

## Como funciona

- O middleware `StartAuditOperation`, no grupo `web`, abre uma operação por requisição e guarda rota, método, URL (sem query string), IP e navegador para o log técnico.
- Models que implementam `Auditable` e usam a trait `AuditsChanges` são observados pelo `AuditObserver` (`created`, `updated`, `deleting`/`deleted`, `restored`).
- `RecordAuditChangeAction` grava em `audit_records` dentro da mesma transação da operação: se ela for desfeita, o registro vai junto. Várias gravações do mesmo registro na mesma requisição viram uma linha só; campo que volta ao valor inicial sai do registro.
- O autor é o usuário autenticado no momento da gravação. No "entrar como", o administrador original fica em `impersonator_*` e a frase mostra "Admin em nome de Maria". Sem usuário (comandos agendados), o autor é "Sistema".
- Nomes de chaves estrangeiras e de listas de ids são gravados no momento da mudança (`referenced_names`), então o histórico mostra o nome que valia mesmo após renomear ou excluir o registro apontado. Quando o nome mora em outra tabela, use o caminho pela relação, como `person.name`.
- A tela apresenta cada registro em linguagem simples e alterna para o log técnico cru, sem nova requisição.

## Tornar um model auditável

1. `implements Auditable` e `use AuditsChanges;` (`app/Domains/Audit`).
2. `auditType()`: apelido estável em snake_case. Nunca mude depois de publicado, pois os registros antigos dependem dele.
3. `auditLabel()`: nome do tipo em pt-BR, usado no filtro.
4. `auditedFields()`: lista branca de colunas com rótulo e formato (`AuditedField::text`, `richText`, `number`, `money`, `date`, `dateTime`, `time`, `boolean`, `enum`, `reference`, `list`, `file`, `secret`). Senhas e tokens usam `secret`: o valor nunca é gravado.
5. `unauditedFields()`: colunas deliberadamente fora, além de `id` e timestamps. Toda coluna nova precisa entrar em uma das duas listas.
6. `auditDescription()`: como o registro aparece na frase, com artigo ("o aluno Maria Souza").
7. Registre a classe em `AuditableEntities` no provider do próprio domínio (Auth, Backup, Inclusion, Website). Sem o registro, o tipo não aparece no filtro e os rótulos não são resolvidos.

## Escritas fora dos eventos do Eloquent

`sync()`, `attach()` e `detach()` em pivôs sem classe própria, `query()->update()`, `insert` e `DB::table()` não disparam eventos. Nesses casos:

- Declare o campo como `AuditedField::list(..., model: X::class)` e devolva os ids atuais em `auditExtraValues()`, para que entrem nas fotos de criação e exclusão.
- Leia os ids antes, faça a escrita e chame `$model->recordAuditFieldChange('campo', $antes, $depois)`. A mudança é fundida com o registro do model na mesma operação: "criou o cargo X" já traz as permissões.
- Prefira `save()` por registro a escritas em massa quando o volume for pequeno.

## Fora de escopo

- Auditoria de leituras, logins e falhas de autenticação.
- Retenção ou expurgo automático e exportação do histórico.
- Histórico de um registro a partir da tela dele.
