[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$repositoryRoot = Split-Path -Parent $PSScriptRoot
$productionCompose = @(
    'compose',
    '-p', 'ralmaster-redesign-production',
    '-f', 'compose.production.yml'
)

function Invoke-DockerChecked {
    param(
        [Parameter(Mandatory)]
        [string[]] $Arguments
    )

    & docker @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "Docker command failed with exit code $LASTEXITCODE."
    }
}

Push-Location -LiteralPath $repositoryRoot
try {
    Invoke-DockerChecked -Arguments ($productionCompose + @('up', '-d', '--wait', 'db'))

    $mysqlCount = 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysql -N -u"$MYSQL_USER" "$MYSQL_DATABASE" -e ''SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE()'''

    $sourceTableCount = & docker compose exec -T db sh -lc $mysqlCount
    if ($LASTEXITCODE -ne 0) {
        throw 'Unable to read the development database table count.'
    }

    $destinationTableCount = & docker @productionCompose exec -T db sh -lc $mysqlCount
    if ($LASTEXITCODE -ne 0) {
        throw 'Unable to read the production-like database table count.'
    }

    $sourceTableCount = [int]($sourceTableCount | Select-Object -Last 1)
    $destinationTableCount = [int]($destinationTableCount | Select-Object -Last 1)

    if ($sourceTableCount -eq 0) {
        throw 'The development database is empty; refusing to clone it.'
    }

    if ($destinationTableCount -ne 0) {
        throw "The production-like database already contains $destinationTableCount tables; refusing to overwrite it."
    }

    $sourceContainerId = & docker compose ps -q db
    $destinationContainerId = & docker @productionCompose ps -q db
    if (-not $sourceContainerId -or -not $destinationContainerId) {
        throw 'Both development and production-like database containers must be running.'
    }

    $sourceContainer = (& docker inspect --format '{{.Name}}' $sourceContainerId).TrimStart('/')
    $sourceInspect = (& docker inspect $sourceContainerId | ConvertFrom-Json)[0]
    $sourceNetwork = $sourceInspect.NetworkSettings.Networks.PSObject.Properties.Name | Select-Object -First 1
    if (-not $sourceNetwork) {
        throw 'Unable to determine the development database Docker network.'
    }

    Invoke-DockerChecked -Arguments @('network', 'connect', $sourceNetwork, $destinationContainerId)
    try {
        $cloneCommand = @'
MYSQL_PWD="$MYSQL_PASSWORD" mysqldump --single-transaction --quick --skip-lock-tables --no-tablespaces --set-gtid-purged=OFF --hex-blob --default-character-set=utf8mb4 -h "$SOURCE_DB_HOST" -u"$MYSQL_USER" "$MYSQL_DATABASE" | MYSQL_PWD="$MYSQL_PASSWORD" mysql --default-character-set=utf8mb4 -h 127.0.0.1 -u"$MYSQL_USER" "$MYSQL_DATABASE"
'@
        & docker exec -e "SOURCE_DB_HOST=$sourceContainer" $destinationContainerId bash -o pipefail -c $cloneCommand
        if ($LASTEXITCODE -ne 0) {
            throw 'The in-Docker production-like database clone failed.'
        }
    }
    finally {
        & docker network disconnect $sourceNetwork $destinationContainerId | Out-Null
        if ($LASTEXITCODE -ne 0) {
            Write-Warning 'Unable to disconnect the production-like database from the development network.'
        }
    }

    $clonedTableCount = & docker @productionCompose exec -T db sh -lc $mysqlCount
    if ($LASTEXITCODE -ne 0) {
        throw 'Unable to verify the cloned production-like database.'
    }
    $clonedTableCount = [int]($clonedTableCount | Select-Object -Last 1)

    if ($clonedTableCount -ne $sourceTableCount) {
        throw "Clone verification failed: source has $sourceTableCount tables, destination has $clonedTableCount."
    }

    Write-Output "Production-like database clone verified: $clonedTableCount tables."
}
finally {
    Pop-Location
}
