$ErrorActionPreference = "Stop"

. "$PSScriptRoot\scripts\localwp-env.ps1"

Write-Host "Testing LocalWP..."

Invoke-LocalWp "option" "get" "home"

Write-Host ""

Invoke-LocalWp "post" "list" "--post_type=page" "--fields=ID,post_title,post_name,post_status"

Write-Host ""
Write-Host "LocalWP WP-CLI test successful."