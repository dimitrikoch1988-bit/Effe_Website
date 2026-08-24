param(
    [string]$ContentPath = (Join-Path $PSScriptRoot "content\pages"),
    [switch]$DryRun,
    [switch]$Apply
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

if ($DryRun -and $Apply) {
    throw "Use either -DryRun or -Apply, not both."
}

# Safe by default: a database write requires the explicit -Apply switch.
$IsDryRun = -not $Apply

. (Join-Path $PSScriptRoot "scripts\localwp-env.ps1")

function Read-ContentPage {
    param([string]$Path)

    $raw = [System.IO.File]::ReadAllText($Path)
    $match = [regex]::Match($raw, "\A---\r?\n(?<metadata>[\s\S]*?)\r?\n---\r?\n(?<content>[\s\S]*)\z")

    if (-not $match.Success) {
        throw "Invalid content file '$Path'. Expected front matter between --- lines."
    }

    $metadata = @{}
    foreach ($line in ($match.Groups["metadata"].Value -split "\r?\n")) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        $separator = $line.IndexOf(":")
        if ($separator -lt 1) { throw "Invalid metadata line '$line' in '$Path'." }

        $key = $line.Substring(0, $separator).Trim()
        $value = $line.Substring($separator + 1).Trim()
        if ($metadata.ContainsKey($key)) { throw "Duplicate metadata key '$key' in '$Path'." }
        $metadata[$key] = $value
    }

    foreach ($required in @("slug", "title", "status")) {
        if (-not $metadata.ContainsKey($required) -or [string]::IsNullOrWhiteSpace($metadata[$required])) {
            throw "Content file '$Path' is missing required metadata '$required'."
        }
    }

    $slug = $metadata["slug"]
    $status = $metadata["status"].ToLowerInvariant()
    if ($slug -notmatch "^[a-z0-9]+(?:-[a-z0-9]+)*$") {
        throw "Invalid slug '$slug' in '$Path'. Use lowercase letters, numbers, and hyphens."
    }
    if ($status -notin @("draft", "publish", "pending", "private", "future")) {
        throw "Invalid WordPress status '$status' in '$Path'."
    }
    if ($slug -eq "agb" -and $status -ne "draft") {
        throw "The existing AGB page must remain draft."
    }
    if ($slug -eq "home") {
        throw "The homepage is not managed by this workflow yet."
    }

    [pscustomobject]@{
        Path    = $Path
        Slug    = $slug
        Title   = $metadata["title"]
        Status  = $status
        Content = $match.Groups["content"].Value.TrimEnd("`r", "`n")
    }
}

function Get-LocalPages {
    $previousErrorActionPreference = $ErrorActionPreference
    try {
        # LocalWP currently emits a harmless PHP imagick warning on stderr.
        $ErrorActionPreference = "Continue"
        $output = @(Invoke-LocalWp "post" "list" "--post_type=page" "--fields=ID,post_title,post_name,post_status,post_content" "--format=json" 2>$null)
        # Keep only WP-CLI's JSON line if PHP writes a startup warning to stderr.
        $json = @($output | Where-Object { $_ -match "^\s*\[" } | Select-Object -Last 1) -join "`n"
    }
    finally {
        $ErrorActionPreference = $previousErrorActionPreference
    }
    if ([string]::IsNullOrWhiteSpace($json)) { return @() }
    $parsed = ConvertFrom-Json $json
    if ($parsed -is [array]) { return @($parsed) }

    # Windows PowerShell can expose a JSON list as one object whose fields are arrays.
    if ($parsed.ID -is [array]) {
        $normalized = for ($index = 0; $index -lt @($parsed.ID).Count; $index++) {
            [pscustomobject]@{
                ID           = @($parsed.ID)[$index]
                post_title   = @($parsed.post_title)[$index]
                post_name    = @($parsed.post_name)[$index]
                post_status  = @($parsed.post_status)[$index]
                post_content = @($parsed.post_content)[$index]
            }
        }
        return @($normalized)
    }

    return @($parsed)
}

if (-not (Test-Path -LiteralPath $ContentPath -PathType Container)) {
    throw "Content directory not found: $ContentPath"
}

$files = @(Get-ChildItem -LiteralPath $ContentPath -File -Filter "*.html" | Sort-Object Name)
if ($files.Count -eq 0) {
    Write-Host "No managed .html content files found in $ContentPath."
    exit 0
}

$pages = @($files | ForEach-Object { Read-ContentPage $_.FullName })
$duplicateSlugs = @($pages | Group-Object Slug | Where-Object Count -gt 1)
if ($duplicateSlugs.Count -gt 0) {
    throw "Duplicate content slugs: $($duplicateSlugs.Name -join ', ')"
}

Write-Host "Reading LocalWP pages..."
$existingPages = @(Get-LocalPages)

foreach ($page in $pages) {
    $matches = @($existingPages | Where-Object { $_.post_name -eq $page.Slug })
    if ($matches.Count -gt 1) { throw "More than one local page has exact slug '$($page.Slug)'." }

    if ($matches.Count -eq 0) {
        Write-Host "CREATE  /$($page.Slug)/ (title='$($page.Title)', status=$($page.Status), content=$($page.Content.Length) chars)"
        if (-not $IsDryRun) {
            Invoke-LocalWp "post" "create" "--post_type=page" "--post_name=$($page.Slug)" "--post_title=$($page.Title)" "--post_status=$($page.Status)" "--post_content=$($page.Content)" "--porcelain"
        }
        continue
    }

    $existing = $matches[0]
    $changes = @()
    if ($existing.post_title -cne $page.Title) { $changes += "title: '$($existing.post_title)' -> '$($page.Title)'" }
    if ($existing.post_status -cne $page.Status) { $changes += "status: $($existing.post_status) -> $($page.Status)" }
    if ($existing.post_content -cne $page.Content) { $changes += "content: $($existing.post_content.Length) -> $($page.Content.Length) chars" }

    if ($changes.Count -eq 0) {
        Write-Host "OK      /$($page.Slug)/ (no changes)"
        continue
    }

    Write-Host "UPDATE  /$($page.Slug)/ (ID=$($existing.ID))"
    $changes | ForEach-Object { Write-Host "        $_" }
    if (-not $IsDryRun) {
        Invoke-LocalWp "post" "update" "$($existing.ID)" "--post_title=$($page.Title)" "--post_status=$($page.Status)" "--post_content=$($page.Content)"
    }
}

if ($IsDryRun) {
    Write-Host ""
    Write-Host "Dry run complete. No WordPress database changes were made."
} else {
    Write-Host ""
    Write-Host "LocalWP content synchronization complete. Production/IONOS was not contacted."
}
