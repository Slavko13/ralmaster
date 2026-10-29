[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$repositoryRoot = (Resolve-Path -LiteralPath (Split-Path -Parent $PSScriptRoot)).Path
$cacheTargets = @(
    'bitrix\cache',
    'bitrix\managed_cache',
    'bitrix\stack_cache'
)

foreach ($relativeTarget in $cacheTargets) {
    $target = Join-Path $repositoryRoot $relativeTarget
    $resolvedTarget = (Resolve-Path -LiteralPath $target).Path
    $expectedPrefix = $repositoryRoot.TrimEnd('\') + '\'

    if (-not $resolvedTarget.StartsWith($expectedPrefix, [StringComparison]::OrdinalIgnoreCase)) {
        throw "Refusing to clear a path outside the repository: $resolvedTarget"
    }

    Get-ChildItem -Force -LiteralPath $resolvedTarget |
        Where-Object Name -ne '.gitkeep' |
        Remove-Item -Recurse -Force
}

Write-Output 'Local Bitrix cache directories cleared safely.'
