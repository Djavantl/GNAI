# Backup com Spatie Laravel Backup em Docker (Guia de Correção)

Este guia resolve o problema em que o backup "reseta a página" ou falha silenciosamente ao usar **spatie/laravel-backup** em ambiente **Docker**.

---

## 1. Verificar logs do Laravel (PASSO OBRIGATÓRIO)

Sempre comece pelos logs.

```bash
tail -f storage/logs/laravel.log
```

Depois, tente gerar o backup novamente.

Erros comuns encontrados:
- `mysqldump: command not found`
- `Permission denied`
- `ZipArchive not found`

---

## 2. Garantir mysql-client no container PHP

O pacote **Spatie Backup** precisa do `mysqldump` dentro do **container app (PHP)**.

No seu `Dockerfile`, certifique-se de ter:

```dockerfile
RUN apt-get update && apt-get install -y     mysql-client     zip     unzip
```

Depois, **rebuild o container**:

```bash
docker compose build --no-cache
docker compose up -d
```

---

## 3. Informar o caminho do mysqldump ao Laravel

Edite o arquivo:

```php
config/database.php
```

Na conexão `mysql`, adicione:

```php
'mysql' => [
    // ...
    'dump' => [
        'dump_binary_path' => '/usr/bin',
        'use_single_transaction',
        'timeout' => 60 * 5,
    ],
],
```

Depois rode:

```bash
php artisan config:clear
```

---

## 4. Corrigir permissões das pastas (MUITO IMPORTANTE)

Entre no container:

```bash
docker exec -it gnai_app sh
```

Teste escrita:

```bash
touch storage/app/test.txt
```

Se falhar, execute:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Saia do container:

```bash
exit
```

---

## 5. Conferir pasta de destino do backup

O Spatie cria backups em:

```
storage/app/{nome-do-backup}
```

No arquivo `config/backup.php`, verifique:

```php
'name' => 'GNAI',
```

O caminho final será:

```
storage/app/GNAI/
```

Garanta que a pasta existe (ou deixe o pacote criar).

---

## 6. Testar o comando manualmente (ESSENCIAL)

Dentro do container:

```bash
docker exec -it gnai_app php artisan backup:run
```

Se aparecer erro aqui, ele é o erro real do problema.

---

## 7. BackupService com tratamento de erro (RECOMENDADO)

No seu serviço de backup:

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

public function generate()
{
    $exitCode = Artisan::call('backup:run', [
        '--disable-notifications' => true
    ]);

    $output = Artisan::output();

    if ($exitCode !== 0) {
        Log::error('Erro ao gerar backup: ' . $output);
        throw new \Exception('Falha ao gerar backup.');
    }
}
```

Isso evita falhas silenciosas.

---

## 8. Limpeza final (opcional)

```bash
php artisan optimize:clear
```

---

## Checklist Final

- [ ] mysql-client instalado no container PHP  
- [ ] dump_binary_path configurado  
- [ ] Permissões corretas em storage/  
- [ ] Comando funciona via terminal  
- [ ] Logs sem erro  

---

## Resultado esperado

Após executar todos os passos, o backup será gerado corretamente em:

```
storage/app/GNAI/*.zip
```

E o botão da aplicação **não irá mais resetar a página**.

---

✔ Guia validado para Docker + Laravel + Spatie Backup
