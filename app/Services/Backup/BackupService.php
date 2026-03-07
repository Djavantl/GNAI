<?php

namespace App\Services\Backup;

use App\Models\Backup\Backup;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class BackupService {
    protected $disk;

    public function __construct() {
        $this->disk = Storage::disk('local');
    }

    /**
     * Executa o comando de backup do sistema e registra o resultado no banco de dados.
     * * Este metodo aciona o pacote Spatie Backup para gerar um ZIP contendo o dump
     * do banco de dados e arquivos de mídia, identifica o arquivo recém-criado
     * e cria um registro na tabela de backups do GNAI.
     *
     * @return \App\Models\Backup\Backup
     * @throws \Exception Caso o comando seja executado, mas o arquivo não seja localizado no disco.
     * @throws \Throwable Repassa exceções ocorridas durante o processo de execução ou persistência.
     */
    public function generate(): Backup
    {
        try {
            // Aciona o comando via Artisan sem disparar notificações (e-mail/slack)
            Artisan::call('backup:run', [
                '--disable-notifications' => true
            ]);

            $backupFolder = config('backup.backups.name');

            // Varre o disco em busca dos arquivos gerados pelo comando
            $files = $this->disk->allFiles($backupFolder);

            // Filtra apenas arquivos ZIP e ordena pelo mais recente (Data de Modificação)
            $latestFile = collect($files)
                ->filter(fn ($file) => str_ends_with($file, '.zip'))
                ->sortByDesc(fn ($file) => $this->disk->lastModified($file))
                ->first();

            if ($latestFile) {
                // Persiste as informações do arquivo físico na base de dados do sistema
                return Backup::create([
                    'file_name' => basename($latestFile),
                    'file_path' => $latestFile,
                    'size'      => $this->formatBytes($this->disk->size($latestFile)),
                    'status'    => 'success',
                    'user_id'   => Auth::id(),
                ]);
            }

            throw new Exception("Comando executado, mas o arquivo ZIP não foi encontrado na pasta {$backupFolder}.");

        } catch (Exception $e) {
            Log::error("Erro no BackupService: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Processa o upload manual de um arquivo de backup e o registra no sistema.
     * * Este metodo recebe um arquivo enviado via formulário, armazena-o no disco local
     * dentro da pasta de backups padronizada e cria o registro correspondente no
     * banco de dados para que o arquivo fique visível e disponível para restauração.
     *
     * @param \Illuminate\Http\UploadedFile $file O arquivo ZIP proveniente da requisição.
     * @return \App\Models\Backup\Backup O modelo do backup persistido no banco de dados.
     * @throws \Exception Caso ocorra uma falha na escrita do arquivo ou na persistência dos dados.
     */
    public function storeUploadedFile($file): Backup
    {
        try {
            $fileName = $file->getClientOriginalName();

            // Define o diretório de destino padronizado para o sistema GNAI
            $destinationPath = 'GNAIbackups';

            // 1. Armazena o arquivo fisicamente. O Laravel cria o diretório automaticamente se necessário.
            $path = $this->disk->putFileAs($destinationPath, $file, $fileName);

            // 2. Registra os metadados do arquivo na base de dados
            return Backup::create([
                'file_name' => $fileName,
                'file_path' => $path,
                'size'      => $this->formatBytes($file->getSize()),
                'status'    => 'success',
                'user_id'   => Auth::id() ?? 1, // Fallback para o ID 1 caso não haja sessão (ex: via API/Seed)
            ]);
        } catch (Exception $e) {
            Log::error("Erro físico no upload do BackupService: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove permanentemente um backup do sistema, eliminando o arquivo físico e o registro.
     * * Este metodo localiza o backup pelo ID, verifica a existência do arquivo no disco
     * local para realizar a exclusão física e, por fim, remove o registro da base de
     * dados do GNAI, garantindo a limpeza completa do armazenamento.
     *
     * @param int|string $id O identificador único do backup a ser removido.
     * @return bool|null Retorna verdadeiro se a exclusão do registro no banco for bem-sucedida.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Caso o ID não exista no banco.
     */
    public function delete($id): ?bool
    {
        // Localiza o modelo ou retorna erro 404 caso não encontre
        $backup = Backup::findOrFail($id);

        // 1. Verifica e remove o arquivo físico do Storage para liberar espaço em disco
        if ($this->disk->exists($backup->file_path)) {
            $this->disk->delete($backup->file_path);
        }

        // 2. Remove o registro lógico da base de dados
        return $backup->delete();
    }

    /**
     * Formata um valor em bytes para uma representação legível em unidades de medida.
     * * Este metodo converte tamanhos de arquivos de forma dinâmica entre
     * Bytes (B), Kilobytes (KB), Megabytes (MB), Gigabytes (GB) e Terabytes (TB),
     * aplicando o cálculo logarítmico na base 1024 e arredondando para a precisão desejada.
     *
     * @param int|float $bytes O valor bruto do tamanho do arquivo em bytes.
     * @param int $precision A quantidade de casas decimais para o arredondamento (Padrão: 2).
     * @return string O valor formatado seguido da respectiva unidade (ex: "2.35 MB").
     */
    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = (float) max($bytes, 0);

        if ($bytes === 0.0) {
            return '0 B';
        }

        // Calcula a potência de 1024 correspondente à unidade de medida
        $power = (int) floor(log($bytes, 1024));

        // Garante que a unidade não ultrapasse o array de unidades disponíveis
        $power = min($power, count($units) - 1);

        // Realiza a conversão e o arredondamento
        $value = (float) ($bytes / (1024 ** $power));
        $rounded = (float) round($value, $precision);

        return $rounded . ' ' . $units[$power];
    }

    /**
     * Sincroniza os arquivos físicos de backup no disco com os registros no banco de dados.
     * * Este metodo realiza uma varredura na pasta de backups, cadastrando novos arquivos
     * encontrados (como backups automáticos do Spatie ou via FTP) e removendo registros
     * cujos arquivos físicos não existem mais. Isso garante que a interface do usuário
     * reflita sempre o estado real do armazenamento.
     *
     * @return bool Retorna verdadeiro em caso de sucesso ou falso se ocorrer uma exceção.
     */
    public function sync(): bool
    {
        try {
            $backupFolder = config('backup.backups.name'); // private/GNAIbackups

            // 1. Pega todos os arquivos ZIP da pasta física para comparação
            $filesOnDisk = $this->disk->allFiles($backupFolder);
            $zipFiles = array_filter($filesOnDisk, fn($file) => str_ends_with($file, '.zip'));

            foreach ($zipFiles as $file) {
                $fileName = basename($file);

                // 2. Verifica se este arquivo já está registrado na base de dados do GNAI
                $exists = Backup::where('file_name', $fileName)->exists();

                if (!$exists) {
                    // 3. Se o arquivo existe no disco mas não no banco, realiza o cadastro automático
                    Backup::create([
                        'file_name' => $fileName,
                        'file_path' => $file,
                        'size'      => $this->formatBytes($this->disk->size($file)),
                        'status'    => 'success',
                        'user_id'   => Auth::id() ?? 1, // Atribui ao administrador padrão se não houver sessão
                    ]);
                }
            }

            // 4. Limpeza lógica: Remove do banco registros de arquivos que foram deletados manualmente do disco
            $backupsInDb = Backup::all();
            foreach ($backupsInDb as $dbBackup) {
                if (!$this->disk->exists($dbBackup->file_path)) {
                    $dbBackup->delete();
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("Erro ao sincronizar backups: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Realiza a restauração completa do sistema (Banco de Dados e Arquivos).
     * * Este metodo executa um processo crítico de recuperação de desastres que inclui:
     * 1. Localização e validação do arquivo ZIP em múltiplos ambientes (Docker/Local).
     * 2. Extração de arquivos para diretório temporário.
     * 3. Injeção automatizada do dump SQL via CLI (mariadb/mysql).
     * 4. Restauração física dos arquivos de mídia (storage/app).
     * 5. Limpeza obrigatória de resíduos temporários (Garbage Collection).
     *
     * @param int|string $id Identificador do backup no banco de dados.
     * @return bool Retorna verdadeiro se o processo for concluído com sucesso.
     * @throws \Exception Caso ocorram falhas em qualquer uma das etapas críticas.
     */
    public function restore($id): bool
    {
        // Localiza o registro do backup no banco de dados do sistema GNAI
        $backup = Backup::findOrFail($id);

        // --- ETAPA 1: LOCALIZAÇÃO DO ARQUIVO FÍSICO ---
        $fileName = $backup->file_name;

        // Normaliza caminhos vindos do Windows (\) para o padrão Linux/Docker (/)
        $pathFromDatabase = str_replace('\\', '/', $backup->file_path);
        $pathFromDatabase = str_replace('storage/app/', '', $pathFromDatabase);

        // Define caminhos prováveis onde o arquivo ZIP pode estar (Lógica Multi-Ambiente)
        $naiPath = 'private/GNAIbackups/' . $fileName;
        $possiblePaths = [
            storage_path('app/' . $pathFromDatabase),
            storage_path('app/' . $naiPath),
            storage_path('app/GNAIbackups/' . $fileName),
        ];

        $zipPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $zipPath = $path;
                break;
            }
        }

        // Se após checar todos os caminhos o arquivo não for achado, interrompe o processo
        if (!$zipPath) {
            Log::error("Arquivo de Backup não encontrado no servidor. Nome: " . $fileName);
            throw new Exception("Arquivo físico não encontrado: " . $fileName);
        }

        // Previne que o PHP encerre a conexão em backups grandes (Tempo limite: 5 minutos)
        set_time_limit(300);

        // Cria um diretório temporário único baseado no Timestamp para extração
        $tempPath = storage_path('app/restore-temp-' . time());
        $zip = new ZipArchive;

        try {
            // --- ETAPA 2: EXTRAÇÃO DOS DADOS ---
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempPath);
                $zip->close();
            } else {
                throw new Exception("Falha ao abrir o arquivo ZIP de backup.");
            }

            // --- ETAPA 3: RESTAURAÇÃO DO BANCO DE DADOS (SQL INJECTION) ---
            // Percorre a pasta extraída procurando recursivamente pelo arquivo .sql (Dump do banco)
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempPath));
            $sqlFile = null;
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $sqlFile = $file->getRealPath();
                    break;
                }
            }

            if ($sqlFile) {
                $dbConfig = config('database.connections.mysql');

                // Comando Shell otimizado para Docker/MariaDB:
                // -h: Host do banco (serviço 'db')
                // -p: Senha (colada ao parâmetro conforme exigência do MariaDB)
                // --ssl=0: Desativa SSL para evitar erros de certificado na rede interna Docker
                // 2>&1: Redireciona erros para que o PHP possa capturá-los
                $command = sprintf(
                    'mariadb -h %s -u %s -p%s %s --ssl=0 < %s 2>&1',
                    $dbConfig['host'],
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $dbConfig['database'],
                    escapeshellarg($sqlFile)
                );

                // Executa o comando no sistema operacional (Alpine Linux do container)
                exec($command, $output, $returnVar);

                // Se o código de retorno (returnVar) não for 0, houve um erro crítico na importação
                if ($returnVar !== 0) {
                    // Filtramos avisos de "Deprecated" para encontrar a mensagem de erro real
                    $filteredError = collect($output)
                        ->reject(fn($line) => str_contains($line, 'Deprecated'))
                        ->first();

                    if ($filteredError) {
                        Log::error("Erro na Restauração SQL do GNAI: " . $filteredError);
                        throw new Exception("Erro ao importar o SQL: " . $filteredError);
                    }
                }
            }

            // --- ETAPA 4: RESTAURAÇÃO DE ARQUIVOS DE MÍDIA (STORAGE) ---
            // Localiza a pasta de arquivos dentro da estrutura gerada pelo Spatie
            $sourceStorage = $tempPath . '/var/www/storage/app';

            if (is_dir($sourceStorage)) {
                $destination = storage_path('app');

                // Se estiver no Windows (Local), usa xcopy. No Linux (Docker), usa cp -R (mais performático)
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec("xcopy /E /I /Y " . escapeshellarg($sourceStorage) . " " . escapeshellarg($destination));
                } else {
                    // Copia recursivamente todos os arquivos (fotos das barreiras, docs, etc)
                    exec("cp -R " . escapeshellarg($sourceStorage) . "/* " . escapeshellarg($destination) . "/");
                }
            }

            return true;

        } catch (Exception $e) {
            Log::error("Falha Crítica no Restore: " . $e->getMessage());
            throw $e;
        } finally {
            // --- ETAPA 5: LIMPEZA (GARBAGE COLLECTION) ---
            // Independente de erro ou sucesso, removemos a pasta temporária para não lotar o disco
            if (is_dir($tempPath)) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    exec("rd /s /q " . escapeshellarg($tempPath));
                } else {
                    exec("rm -rf " . escapeshellarg($tempPath));
                }
            }
        }
    }
}
