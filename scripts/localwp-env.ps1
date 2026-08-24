$ErrorActionPreference = "Stop"

$script:LocalWpRoot = "C:\Users\Jimbo\Documents\effe_website_local\app\public"
$script:LocalWpCli  = "C:\Users\Jimbo\AppData\Local\Programs\Local\resources\extraResources\bin\wp-cli\win32\wp.bat"
$script:LocalPhpDir = "C:\Users\Jimbo\AppData\Roaming\Local\lightning-services\php-8.2.29+0\bin\win64"
$script:LocalRunRoot = "C:\Users\Jimbo\AppData\Roaming\Local\run"

function Initialize-LocalWpEnvironment {

    if (-not (Test-Path $script:LocalWpRoot)) {
        throw "LocalWP WordPress root not found: $script:LocalWpRoot"
    }

    if (-not (Test-Path $script:LocalWpCli)) {
        throw "LocalWP WP-CLI not found: $script:LocalWpCli"
    }

    if (-not (Test-Path (Join-Path $script:LocalPhpDir "php.exe"))) {
        throw "LocalWP PHP not found: $script:LocalPhpDir"
    }

    $phpIni = Get-ChildItem `
        -Path $script:LocalRunRoot `
        -Filter "php.ini" `
        -Recurse `
        -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTime -Descending |
        Select-Object -First 1

    if (-not $phpIni) {
        throw "Could not find LocalWP php.ini. Make sure the LocalWP site is running."
    }

    $env:PATH = "$script:LocalPhpDir;$env:PATH"
    $env:PHPRC = $phpIni.DirectoryName
}

function Invoke-LocalWp {
    param(
        [Parameter(ValueFromRemainingArguments = $true)]
        [string[]]$Arguments
    )

    Initialize-LocalWpEnvironment

    Push-Location $script:LocalWpRoot

    try {
        & $script:LocalWpCli @Arguments

        if ($LASTEXITCODE -ne 0) {
            throw "WP-CLI command failed with exit code $LASTEXITCODE."
        }
    }
    finally {
        Pop-Location
    }
}