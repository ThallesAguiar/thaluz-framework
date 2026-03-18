# Monitor de arquivos para o thaluz (Windows)
$path = "."
$filter = "*.php"

$fsw = New-Object IO.FileSystemWatcher $path, $filter -Property @{
    IncludeSubdirectories = $true
    EnableRaisingEvents = $true
}

Write-Host "Iniciando thaluz com Watch mode (Windows)..." -ForegroundColor Cyan

function Start-Server {
    if ($global:serverProcess) {
        Stop-Process -Id $global:serverProcess.Id -ErrorAction SilentlyContinue
    }
    $global:serverProcess = Start-Process php -ArgumentList "serve.php" -PassThru -WindowStyle Hidden
    Write-Host "[$(Get-Date -Format HH:mm:ss)] Servidor (re)iniciado." -ForegroundColor Green
}

# Inicia o servidor pela primeira vez
Start-Server

# Eventos de alteração
$action = {
    Write-Host "[$(Get-Date -Format HH:mm:ss)] Mudança detectada em $($Event.SourceEventArgs.FullPath). Reiniciando..." -ForegroundColor Yellow
    Start-Server
}

Register-ObjectEvent $fsw Changed -Action $action
Register-ObjectEvent $fsw Created -Action $action
Register-ObjectEvent $fsw Deleted -Action $action

# Mantém o script rodando
try {
    while ($true) { Start-Sleep 1 }
} finally {
    if ($global:serverProcess) {
        Stop-Process -Id $global:serverProcess.Id -ErrorAction SilentlyContinue
    }
    Unregister-Event -SourceIdentifier *
}
