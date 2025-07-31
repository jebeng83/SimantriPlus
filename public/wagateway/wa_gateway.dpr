program wa_gateway;

uses
  Vcl.Forms,
  ufrmUtama in 'ufrmUtama.pas' {frmUtama},
  ufrmDDL in 'ufrmDDL.pas' {frmDDL},
  ufrmSetPort in 'ufrmSetPort.pas' {frmSetPort},
  ufrmLastQuery in 'ufrmLastQuery.pas' {frmLastQuery};

{$R *.res}

begin
  Application.Initialize;
  Application.MainFormOnTaskbar := True;
  Application.CreateForm(TfrmUtama, frmUtama);
  Application.CreateForm(TfrmDDL, frmDDL);
  Application.CreateForm(TfrmSetPort, frmSetPort);
  Application.CreateForm(TfrmLastQuery, frmLastQuery);
  Application.Run;
end.
