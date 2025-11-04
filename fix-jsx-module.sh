#!/usr/bin/env bash
# fix-jsx-module.sh — Perbaiki error "Failed to load module script" akibat pemuatan file .jsx dari /build/assets
# Penggunaan:
#   ./fix-jsx-module.sh -y
# Opsi:
#   -y / --yes      Jalan non-interaktif
#   --skip-components-case  Lewati penggantian "Components/" -> "components/"
#   --dry-run       Cetak rencana perubahan tanpa menulis file

set -euo pipefail

AUTO="false"
DRYRUN="false"
SKIP_CASE="false"

while [[ $# -gt 0 ]]; do
  case "$1" in
    -y|--yes) AUTO="true"; shift ;;
    --dry-run) DRYRUN="true"; shift ;;
    --skip-components-case) SKIP_CASE="true"; shift ;;
    *) echo "Argumen tidak dikenal: $1"; exit 2 ;;
  esac
done

project_root="$(pwd)"
js_dir="resources/js"
app_file="$js_dir/app.jsx"
timestamp="$(date +%Y%m%d_%H%M%S)"
backup_dir="fix_jsx_mime_backups_$timestamp"
summary="fix_jsx_mime_summary_$timestamp.log"

mkdir -p "$backup_dir"

if [[ ! -f "$app_file" ]]; then
  echo "ERROR: $app_file tidak ditemukan. Jalankan skrip di root proyek Laravel (yang punya resources/js/app.jsx)."
  exit 1
fi

echo "==> Memulai perbaikan. Root proyek: $project_root" | tee "$summary"
echo "==> Backup akan disimpan di: $backup_dir" | tee -a "$summary"

# Hitung masalah awal
count_importif=$(grep -F "importIfPreamble(" "$app_file" | wc -l || true)
count_assets_jsx=$(grep -R -n -E "/build/assets/[^\"')]+\.jsx" "$js_dir" --include="*.js" --include="*.jsx" --include="*.ts" --include="*.tsx" | wc -l || true)
count_case_mismatch=$(grep -R -n -F "Components/" "$js_dir" --include="*.js" --include="*.jsx" --include="*.ts" --include="*.tsx" | wc -l || true)

echo "- Temuan awal:" | tee -a "$summary"
echo "  • importIfPreamble( di app.jsx: $count_importif" | tee -a "$summary"
echo "  • Referensi langsung /build/assets/*.jsx: $count_assets_jsx" | tee -a "$summary"
echo "  • Import 'Components/' bertulis huruf besar: $count_case_mismatch" | tee -a "$summary"

if [[ "$DRYRUN" == "true" ]]; then
  echo "==> Mode dry-run: tidak ada perubahan yang ditulis. Keluar." | tee -a "$summary"
  exit 0
fi

# Backup file yang akan diubah
cp "$app_file" "$backup_dir/app.jsx.bak"

# Util: sed -i portabel
sed_in_place() {
  local sed_args=("$@")
  if sed --version >/dev/null 2>&1; then
    sed -i "${sed_args[@]}"
  else
    sed -i '' "${sed_args[@]}"
  fi
}

# 1) Paksa fungsi importIfPreamble menjadi import langsung
if grep -q "const[[:space:]]\+importIfPreamble" "$app_file"; then
  sed_in_place 's|^[[:space:]]*const[[:space:]]*importIfPreamble[[:space:]]*=[[:space:]]*.*$|const importIfPreamble = (path) => import(path);|' "$app_file"
fi

# 2) Ganti semua pemanggilan importIfPreamble( ... ) menjadi import( ... )
if [[ "$count_importif" -gt 0 ]]; then
  sed_in_place 's|importIfPreamble(|import(|g' "$app_file"
fi

# 3) Hilangkan referensi eksplisit ke /build/assets/*.jsx jika ada
#    Ubah import('/build/assets/.../file.jsx') -> import('./.../file.jsx')
if [[ "$count_assets_jsx" -gt 0 ]]; then
  sed_in_place "s|import\(\s*['\"]/build/assets/\([^"')][^"')]*\)\\.jsx['\"]\s*\)|import('./\1.jsx')|g" "$app_file"
fi

# 4) Perbaiki case pada import 'Components/' -> 'components/' (opsional)
changed_case_files=0
if [[ "$SKIP_CASE" != "true" ]]; then
  while IFS= read -r -d '' f; do
    if grep -Fq "Components/" "$f"; then
      rel="${f#"$project_root/"}"
      mkdir -p "$backup_dir/$(dirname "$rel")"
      cp "$f" "$backup_dir/$rel.bak"
      sed_in_place "s|Components/|components/|g" "$f"
      echo "  • Case-fix: $rel" | tee -a "$summary"
      changed_case_files=$((changed_case_files+1))
    fi
  done < <(find "$js_dir" -type f \( -name "*.js" -o -name "*.jsx" -o -name "*.ts" -o -name "*.tsx" \) -print0)
fi

# Ringkasan perubahan app.jsx
echo "" | tee -a "$summary"
echo "==> Diff app.jsx (ringkas):" | tee -a "$summary"
diff -u "$backup_dir/app.jsx.bak" "$app_file" | sed -n '1,200p' | tee -a "$summary" || true

# 5) Build produksi
echo "" | tee -a "$summary"
echo "==> Menjalankan build produksi: npm run build" | tee -a "$summary"
if npm run build 2>&1 | tee -a "$summary"; then
  echo "==> Build selesai." | tee -a "$summary"
else
  echo "ERROR: Build gagal. Cek log di $summary dan backup di $backup_dir." | tee -a "$summary"
  exit 1
fi

# 6) Bersihkan cache Laravel (opsional tapi dianjurkan)
if command -v php >/dev/null 2>&1 && [[ -f "artisan" ]]; then
  echo "" | tee -a "$summary"
  echo "==> Membersihkan cache Laravel" | tee -a "$summary"
  php artisan view:clear && php artisan route:clear && php artisan config:clear | tee -a "$summary" || true
fi

# 7) Laporan akhir
echo "" | tee -a "$summary"
left_assets_jsx=$(grep -R -n -E "/build/assets/[^\"')]+\.jsx" "$js_dir" --include="*.js" --include="*.jsx" --include="*.ts" --include="*.tsx" | wc -l || true)
left_importif=$(grep -F "importIfPreamble(" "$app_file" | wc -l || true)
left_case=$(grep -R -n -F "Components/" "$js_dir" --include="*.js" --include="*.jsx" --include="*.ts" --include="*.tsx" | wc -l || true)

echo "==> Ringkasan:" | tee -a "$summary"
echo "  • Sisa referensi /build/assets/*.jsx: $left_assets_jsx" | tee -a "$summary"
echo "  • Sisa pemanggilan importIfPreamble(: $left_importif" | tee -a "$summary"
echo "  • Sisa import 'Components/': $left_case" | tee -a "$summary"
echo "" | tee -a "$summary"
echo "Selesai. Silakan hard refresh (Ctrl+F5) pada halaman Home & Farmasi." | tee -a "$summary"