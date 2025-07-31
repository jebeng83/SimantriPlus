unit ufrmLastQuery;

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, System.Classes, Vcl.Graphics,
  Vcl.Controls, Vcl.Forms, Vcl.Dialogs, Vcl.StdCtrls;

type
  TfrmLastQuery = class(TForm)
    memQuery: TMemo;
    btnTUtup: TButton;
    procedure btnTUtupClick(Sender: TObject);
    procedure FormShow(Sender: TObject);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  frmLastQuery: TfrmLastQuery;

implementation

uses
  ufrmUtama;

{$R *.dfm}

procedure TfrmLastQuery.btnTUtupClick(Sender: TObject);
begin
  Close;
end;

procedure TfrmLastQuery.FormShow(Sender: TObject);
begin
  MemQuery.Text := last_query;
end;

end.
