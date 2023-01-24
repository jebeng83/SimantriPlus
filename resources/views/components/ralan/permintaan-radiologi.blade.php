<x-adminlte-card title="Permintaan Radiologi" theme="info" icon="fas fa-lg fa-flask" collapsible="collapsed" maximizable>
    <form id="formPermintaanRadiologi">
        <div class="form-group row">
            <label for="klinis" class="col-sm-4 col-form-label">Klinis</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="klinis" name="klinis" />
            </div>
        </div>
        <div class="form-group row">
            <label for="info" class="col-sm-4 col-form-label">Info Tambahan</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="info" name="info" />
            </div>
        </div>
        <div class="form-group row">
            <label for="jenis" class="col-sm-4 col-form-label">Jenis Pemeriksaan</label>
            <div class="col-sm-8">
              <select class="form-control jenis" id="jenis" name="jenis[]" multiple="multiple" ></select>
            </div>
        </div>
    </form>
</x-adminlte-card>