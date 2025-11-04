#!/usr/bin/env bash
set -euo pipefail

# Script: fix-components-case.sh
# Fungsi:
# - Scan semua file JS/TS untuk menemukan impor dengan 'Components/' (huruf C besar)
# - Perbaiki menjadi 'components/' (huruf c kecil)
# - Simpan backup dan catat ringkasan diff perubahan
# - Jalankan npm run build
#
# Cara pakai:
#   bash fix-components-case.sh -y
# Opsi:
#   -y / --yes : jalankan tanpa prompt konfirmasi (non-interaktif)

TARGET_DIR="${TARGET_DIR:-resources/js}"
BACKUP_DIR="${BACKUP_DIR:-casefix_backup_$(date +%Y%m%d_%H%M%S)}"
SUMMARY_FILE="${SUMMARY_FILE:-casefix_summary_$(date +%Y%m%d_%H%M%S).log}"
AUTO_CONFIRM=0

for arg in "$@"; do
  case "$arg" in
    -y|--yes) AUTO_CONFIRM=1 ;;
    *) ;;
  esac
done

log() { echo "[casefix] $*"; }

# Validasi target dir
if [ ! -d "$TARGET_DIR" ]; then
  log "Target dir tidak ditemukan: $TARGET_DIR"
  exit 1
fi

# Scan awal
before_count=$(grep -R -n 'Components/' "$TARGET_DIR" --include='*.js' --include='*.jsx' --include='*.ts' --include='*.tsx' 2>/dev/null | wc -l || true)
log "Jumlah kemunculan 'Components/' sebelum perbaikan: $before_count"

# Kumpulkan file yang mengandung 'Components/' (tanpa mapfile - kompatibel Bash 3.x)
files=()
filesList=$(grep -R -l 'Components/' "$TARGET_DIR" --include='*.js' --include='*.jsx' --include='*.ts' --include='*.tsx' 2>/dev/null || true)
if [ -n "$filesList" ]; then
  while IFS= read -r line; do
    files+=("$line")
  done <<< "$filesList"
fi

if [ ${#files[@]} -eq 0 ]; then
  log "Tidak ada file yang mengandung 'Components/'. Melanjutkan ke build."
else
  log "Akan memperbarui ${#files[@]} file."

  if [ "$AUTO_CONFIRM" -ne 1 ]; then
    read -r -p "Proceed with GLOBAL REPLACE 'Components/' -> 'components/' ? [y/N] " ans
    case "$ans" in
      y|Y) ;;
      *) log "Dibatalkan oleh pengguna."; exit 0 ;;
    esac
  else
    log "Mode non-interaktif: GLOBAL REPLACE dijalankan otomatis (-y)."
  fi

  mkdir -p "$BACKUP_DIR"
  : > "$SUMMARY_FILE"

  for f in "${files[@]}"; do
    mkdir -p "$BACKUP_DIR/$(dirname "$f")"
    cp "$f" "$BACKUP_DIR/$f"

    tmp="${f}.tmp_casefix"
    # Replace portable: tidak pakai sed -i (beda di Linux/macOS)
    sed 's#Components/#components/#g' "$f" > "$tmp"
    mv "$tmp" "$f"

    {
      echo "==== $f ===="
      diff -u "$BACKUP_DIR/$f" "$f" || true
      echo
    } >> "$SUMMARY_FILE"
  done
fi

# Scan ulang
after_count=$(grep -R -n 'Components/' "$TARGET_DIR" --include='*.js' --include='*.jsx' --include='*.ts' --include='*.tsx' 2>/dev/null | wc -l || true)
log "Jumlah kemunculan 'Components/' setelah perbaikan: $after_count"
[ -d "$BACKUP_DIR" ] && log "Backup disimpan di: $BACKUP_DIR"
[ -f "$SUMMARY_FILE" ] && log "Ringkasan perubahan: $SUMMARY_FILE"

# Verifikasi isi direktori yang relevan (tidak mengganggu bila tidak ada)
log "Memeriksa direktori: resources/js/components/ui"
ls -la resources/js/components/ui || true

# Build
log "Menjalankan build ..."
npm run build && log "Build selesai sukses." || { log "Build gagal."; exit 1; }