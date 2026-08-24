$ErrorActionPreference = "Stop"

$BaseUrl = "http://effe-website-local.local"
$OutDir = Join-Path $PSScriptRoot "seo-audit"

$Pages = @{
    "home.html"        = "/"
    "impressum.html"   = "/impressum/"
    "datenschutz.html" = "/datenschutz/"
}

Write-Host "Checking LocalWP site..."

try {
    $response = Invoke-WebRequest `
        -Uri $BaseUrl `
        -Method Head `
        -TimeoutSec 10 `
        -UseBasicParsing
}
catch {
    Write-Error "LocalWP site is not reachable at $BaseUrl. Start the site in LocalWP and try again."
    exit 1
}

if ($response.StatusCode -lt 200 -or $response.StatusCode -ge 400) {
    Write-Error "LocalWP returned HTTP status $($response.StatusCode)."
    exit 1
}

Write-Host "LocalWP site is reachable."

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null

$tempDir = Join-Path $OutDir "_temp"

if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}

New-Item -ItemType Directory -Path $tempDir | Out-Null

try {
    foreach ($File in $Pages.Keys) {
        $Url = $BaseUrl + $Pages[$File]
        $Target = Join-Path $tempDir $File

        Write-Host "Fetching $Url"

        $pageResponse = Invoke-WebRequest `
            -Uri $Url `
            -TimeoutSec 20 `
            -UseBasicParsing

        if ($pageResponse.StatusCode -lt 200 -or $pageResponse.StatusCode -ge 400) {
            throw "HTTP $($pageResponse.StatusCode) returned for $Url"
        }

        [System.IO.File]::WriteAllText(
            $Target,
            $pageResponse.Content,
            [System.Text.Encoding]::UTF8
        )
    }

    foreach ($File in $Pages.Keys) {
        Move-Item `
            (Join-Path $tempDir $File) `
            (Join-Path $OutDir $File) `
            -Force
    }

    Remove-Item $tempDir -Recurse -Force

    Write-Host ""
    Write-Host "SEO snapshots refreshed successfully:"
    
    foreach ($File in $Pages.Keys) {
        Write-Host " - seo-audit\$File"
    }

    exit 0
}
catch {
    if (Test-Path $tempDir) {
        Remove-Item $tempDir -Recurse -Force
    }

    Write-Error "SEO snapshot refresh failed: $($_.Exception.Message)"
    exit 1
}