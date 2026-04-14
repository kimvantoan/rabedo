# Automation script to prepare and compress Next.js app for cPanel deployment
Write-Host "[START] Starting Next.js Production Build for cPanel..." -ForegroundColor Cyan

# Step 1: Run standard Next.js Build
try {
    npm run build
} catch {
    Write-Host "[ERROR] Build failed. Please check errors." -ForegroundColor Red
    exit 1
}

if (-Not (Test-Path ".next\standalone")) {
    Write-Host "[ERROR] Folders .next\standalone not found. Ensure output: 'standalone' is in next.config.ts" -ForegroundColor Red
    exit 1
}

Write-Host "[SUCCESS] Build Complete! Preparing Standalone package..." -ForegroundColor Green

# Step 2: Copy Static Files and Public Assets to Standalone Box
Copy-Item -Recurse -Force "public" ".next\standalone\public"
Copy-Item -Recurse -Force ".next\static" ".next\standalone\.next\static"

# Step 3: Copy App.js Startup entrypoint for cPanel
Copy-Item -Force "app.js" ".next\standalone\app.js"

# Step 4: Zip everything up!
$DestinationFile = "rabedo-deploy.zip"
if (Test-Path $DestinationFile) {
    Remove-Item $DestinationFile -Force
}

Write-Host "[PACKAGE] Removing conflicting node_modules to satisfy CloudLinux virtualenv..." -ForegroundColor Yellow
if (Test-Path ".next\standalone\node_modules") {
    Remove-Item -Recurse -Force ".next\standalone\node_modules"
}

Write-Host "[PACKAGE] Compressing files into zip archive..." -ForegroundColor Yellow
Compress-Archive -Path ".next\standalone\*" -DestinationPath $DestinationFile -Force

Write-Host "[SUCCESS] ALL DONE! File created: $DestinationFile" -ForegroundColor Green
Write-Host "[INFO] Please upload '$DestinationFile' to your cPanel directory, extract it, and select 'app.js' as your Passenger startup file." -ForegroundColor Magenta
