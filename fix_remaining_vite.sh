#!/bin/bash

echo "Fixing remaining @viteReactRefresh issues..."

# Find all files that still have standalone @viteReactRefresh (not wrapped in environment check)
files=$(grep -r "@viteReactRefresh" resources/views/ | grep -v "app()->environment" | cut -d: -f1 | sort -u)

for file in $files; do
    echo "Processing: $file"
    
    # Check if file already has environment check
    if grep -q "app()->environment.*@viteReactRefresh" "$file"; then
        # Remove standalone @viteReactRefresh if environment check already exists
        sed -i '' '/^[[:space:]]*@viteReactRefresh[[:space:]]*$/d' "$file"
        echo "✓ Removed duplicate @viteReactRefresh from: $file"
    else
        # Replace standalone @viteReactRefresh with environment check
        sed -i '' 's/^[[:space:]]*@viteReactRefresh[[:space:]]*$/    @if(app()->environment('\''local'\'', '\''development'\''))\
        @viteReactRefresh\
    @endif/' "$file"
        echo "✓ Added environment check to: $file"
    fi
done

echo "All remaining files processed!"
echo "Verifying fixes..."

# Check if any standalone @viteReactRefresh still exists
remaining=$(grep -r "@viteReactRefresh" resources/views/ | grep -v "app()->environment" | wc -l)
if [ "$remaining" -eq 0 ]; then
    echo "✅ All @viteReactRefresh directives are now properly wrapped with environment checks!"
else
    echo "⚠️  Still found $remaining files with standalone @viteReactRefresh"
    grep -r "@viteReactRefresh" resources/views/ | grep -v "app()->environment"
fi