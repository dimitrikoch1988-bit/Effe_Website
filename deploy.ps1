$ErrorActionPreference = "Stop"

$HostName = "access-5020957201.webspace-host.com"
$UserName = "su1072882"

$LocalTheme = Join-Path $PSScriptRoot "theme\consted-roofing-flooring"
$RemoteTheme = "~/clickandbuilds/HausmeisterService/wp-content/themes/consted-roofing-flooring"

Write-Host "Deploying Effe Website theme to IONOS..."
Write-Host "Local:  $LocalTheme"
Write-Host "Remote: $RemoteTheme"
Write-Host ""

scp -r "$LocalTheme\*" "${UserName}@${HostName}:${RemoteTheme}/"

if ($LASTEXITCODE -ne 0) {
    throw "Deployment failed."
}

Write-Host ""
Write-Host "Deployment completed successfully."